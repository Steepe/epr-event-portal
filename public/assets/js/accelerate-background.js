(function () {
  const TAU = Math.PI * 2;
  const COLORS = [["#D8198E", 0.4], ["#bd159e", 0.25], ["#EFB11E", 0.15], ["#ffffff", 0.2]];
  const CODE_COLORS = [
    "rgba(216,25,142,0.6)",
    "rgba(239,177,30,0.55)",
    "rgba(255,255,255,0.4)",
    "rgba(189,21,158,0.55)"
  ];

  class AccelerateBackground {
    constructor(stage) {
      this.stage = stage;
      this.inner = stage.querySelector(".rc-stage-inner") || stage;
      this.canvas = document.createElement("canvas");
      this.canvas.className = "rc-bg-canvas";
      this.stage.prepend(this.canvas);
      this.ctx = this.canvas.getContext("2d");
      this.mouse = { x: -9999, y: -9999, on: false };
      this.machines = [];
      this.cables = [];
      this.links = new Map();
      this.rings = [];
      this.ripples = [];
      this.width = 0;
      this.height = 0;
      this.energy = 0;
      this.dim = 1;
      this.last = performance.now();
      this.reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;

      this.resize = this.resize.bind(this);
      this.frame = this.frame.bind(this);
      this.onMove = this.onMove.bind(this);
      this.onDown = this.onDown.bind(this);
      this.onLeave = this.onLeave.bind(this);

      this.resizeObserver = new ResizeObserver(this.resize);
      this.resizeObserver.observe(this.canvas);
      this.resize();

      window.addEventListener("pointermove", this.onMove, { passive: true });
      window.addEventListener("pointerdown", this.onDown, { passive: true });
      document.documentElement.addEventListener("pointerleave", this.onLeave, { passive: true });
      this.raf = requestAnimationFrame(this.frame);
    }

    intensity() {
      return this.reduced ? 0.35 : 1;
    }

    pickColor() {
      let roll = Math.random();
      for (const [color, weight] of COLORS) {
        roll -= weight;
        if (roll <= 0) return color;
      }
      return "#D8198E";
    }

    roundedRect(x, y, w, h, r) {
      const ctx = this.ctx;
      ctx.beginPath();
      if (ctx.roundRect) {
        ctx.roundRect(x, y, w, h, r);
      } else {
        ctx.rect(x, y, w, h);
      }
    }

    line(x1, y1, x2, y2) {
      const ctx = this.ctx;
      ctx.beginPath();
      ctx.moveTo(x1, y1);
      ctx.lineTo(x2, y2);
      ctx.stroke();
    }

    bezier(point, t) {
      const u = 1 - t;
      return {
        x: u * u * u * point[0] + 3 * u * u * t * point[2] + 3 * u * t * t * point[4] + t * t * t * point[6],
        y: u * u * u * point[1] + 3 * u * u * t * point[3] + 3 * u * t * t * point[5] + t * t * t * point[7]
      };
    }

    resize() {
      const dpr = Math.min(2, window.devicePixelRatio || 1);
      this.width = this.canvas.clientWidth;
      this.height = this.canvas.clientHeight;
      this.canvas.width = this.width * dpr;
      this.canvas.height = this.height * dpr;
      this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      this.buildMachines();
    }

    buildMachines() {
      const W = this.width;
      const H = this.height;
      const cellW = 290;
      const cellH = 220;
      const cols = Math.ceil(W / cellW) + 1;
      const rows = Math.ceil(H / cellH) + 1;
      const machines = [];
      const cables = [];

      for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
          if (Math.random() < 0.16) continue;

          const type = ["mon", "mon", "lap", "rack"][Math.floor(Math.random() * 4)];
          const machine = {
            type,
            x: col * cellW + (row % 2 ? cellW / 2 : 0) - cellW * 0.25 + (Math.random() - 0.5) * 70,
            y: row * cellH + 40 + (Math.random() - 0.5) * 50,
            scale: 0.75 + Math.random() * 0.45,
            scroll: Math.random() * 200,
            speed: 5 + Math.random() * 12,
            glow: 0,
            flash: 0,
            blink: Math.random() * 10,
            lines: Array.from({ length: 16 }, () => ({
              indent: Math.floor(Math.random() * 4),
              length: 0.15 + Math.random() * 0.6,
              color: CODE_COLORS[Math.floor(Math.random() * CODE_COLORS.length)]
            }))
          };

          machine.pinX = machine.x;
          machine.pinY = machine.y + (type === "mon" ? 44 : type === "lap" ? 26 : 60) * machine.scale;
          machines.push(machine);
        }
      }

      const seen = new Set();
      machines.forEach((a, index) => {
        machines
          .map((b, nextIndex) => [nextIndex, Math.hypot(b.x - a.x, b.y - a.y)])
          .filter(([nextIndex, distance]) => nextIndex !== index && distance < cellW * 1.5)
          .sort((left, right) => left[1] - right[1])
          .slice(0, 2)
          .forEach(([nextIndex]) => {
            const key = Math.min(index, nextIndex) + "-" + Math.max(index, nextIndex);
            if (seen.has(key)) return;
            seen.add(key);

            const b = machines[nextIndex];
            const sag = Math.max(a.pinY, b.pinY) + 30 + Math.random() * 40;
            cables.push({
              a: index,
              b: nextIndex,
              path: [
                a.pinX,
                a.pinY,
                a.pinX + (b.pinX - a.pinX) * 0.2,
                sag,
                a.pinX + (b.pinX - a.pinX) * 0.8,
                sag,
                b.pinX,
                b.pinY
              ],
              pulses: [],
              next: Math.random() * 3
            });
          });
      });

      this.machines = machines;
      this.cables = cables;
    }

    onMove(event) {
      const rect = this.canvas.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;

      if (this.mouse.on) {
        this.energy = Math.min(8, this.energy + Math.min(60, Math.hypot(x - this.mouse.x, y - this.mouse.y)) * 0.0025);
      }

      this.mouse.x = x;
      this.mouse.y = y;
      this.mouse.on = true;
    }

    onDown(event) {
      const rect = this.canvas.getBoundingClientRect();
      const x = event.clientX - rect.left;
      const y = event.clientY - rect.top;
      this.energy = Math.min(8, this.energy + 2.5);
      this.ripples.push({ x, y, age: 0 });

      let bestIndex = -1;
      let bestDistance = 240;
      this.machines.forEach((machine, index) => {
        const distance = Math.hypot(machine.x - x, machine.y - y);
        if (distance < bestDistance) {
          bestDistance = distance;
          bestIndex = index;
        }
      });

      if (bestIndex >= 0) {
        this.machines[bestIndex].flash = 1;
        this.cables.forEach((cable) => {
          if (cable.a === bestIndex) cable.pulses.push({ t: 0, dir: 1, color: "#EFB11E" });
          if (cable.b === bestIndex) cable.pulses.push({ t: 0, dir: -1, color: "#EFB11E" });
        });
      }
    }

    onLeave() {
      this.mouse.on = false;
    }

    drawCablePulses(delta, intensity) {
      const ctx = this.ctx;

      for (const cable of this.cables) {
        const path = cable.path;
        ctx.beginPath();
        ctx.moveTo(path[0], path[1]);
        ctx.bezierCurveTo(path[2], path[3], path[4], path[5], path[6], path[7]);
        ctx.strokeStyle = "rgba(255,255,255,0.08)";
        ctx.lineWidth = 1.2;
        ctx.stroke();

        cable.next -= delta * intensity * (1 + this.energy * 0.5);
        if (cable.next <= 0) {
          cable.pulses.push({
            t: 0,
            dir: Math.random() < 0.5 ? 1 : -1,
            color: Math.random() < 0.35 ? "#EFB11E" : "#D8198E"
          });
          cable.next = 2 + Math.random() * 4;
        }
      }

      ctx.globalCompositeOperation = "lighter";
      for (const cable of this.cables) {
        for (let index = cable.pulses.length - 1; index >= 0; index--) {
          const pulse = cable.pulses[index];
          pulse.t += delta * (0.28 + 0.12 * intensity) * (1 + this.energy * 0.4);

          const destination = this.machines[pulse.dir > 0 ? cable.b : cable.a];
          const source = this.machines[pulse.dir > 0 ? cable.a : cable.b];

          if (pulse.t >= 1) {
            if (destination) {
              destination.flash = 1;
              destination.flashColor = pulse.color;
            }
            cable.pulses.splice(index, 1);
            continue;
          }

          if (destination && pulse.t > 0.75) {
            destination.near = Math.max(destination.near || 0, (pulse.t - 0.75) / 0.25);
            destination.flashColor = pulse.color;
          }

          if (source && pulse.t < 0.12) {
            source.near = Math.max(source.near || 0, 1 - pulse.t / 0.12);
            source.flashColor = source.flashColor || pulse.color;
          }

          const headT = pulse.dir < 0 ? 1 - pulse.t : pulse.t;
          const head = this.bezier(cable.path, headT);
          const glow = ctx.createRadialGradient(head.x, head.y, 0, head.x, head.y, 18);
          glow.addColorStop(0, pulse.color === "#EFB11E" ? "rgba(239,177,30,0.55)" : "rgba(216,25,142,0.55)");
          glow.addColorStop(1, "rgba(0,0,0,0)");
          ctx.globalAlpha = 1;
          ctx.fillStyle = glow;
          ctx.fillRect(head.x - 18, head.y - 18, 36, 36);

          for (let trail = 0; trail < 8; trail++) {
            let t = Math.max(0, pulse.t - trail * 0.014);
            if (pulse.dir < 0) t = 1 - t;
            const point = this.bezier(cable.path, t);
            ctx.globalAlpha = (1 - trail / 8) * 0.85;
            ctx.beginPath();
            ctx.arc(point.x, point.y, 2.6 - trail * 0.22, 0, TAU);
            ctx.fillStyle = pulse.color;
            ctx.fill();
          }
        }
      }
      ctx.globalAlpha = 1;
      ctx.globalCompositeOperation = "source-over";
    }

    drawMachines(delta, intensity) {
      const ctx = this.ctx;
      ctx.lineCap = "round";
      ctx.lineJoin = "round";

      for (const machine of this.machines) {
        const isNearPointer = this.mouse.on && Math.hypot(machine.x - this.mouse.x, machine.y - this.mouse.y) < 190;
        machine.glow += ((isNearPointer ? 1 : 0) - machine.glow) * Math.min(1, delta * 6);
        machine.flash *= Math.pow(0.3, delta);
        machine.nearSmooth = (machine.nearSmooth || 0) + ((machine.near || 0) - (machine.nearSmooth || 0)) * Math.min(1, delta * 10);
        machine.near = 0;

        const glowStrength = Math.min(1, machine.glow + machine.flash + machine.nearSmooth * 0.6);
        machine.scroll += machine.speed * delta * intensity * (1 + glowStrength * 3 + this.energy * 0.5);
        machine.blink += delta;

        const isGold = (machine.flashColor || "#D8198E") === "#EFB11E" || (machine.glow > machine.flash && machine.glow > machine.nearSmooth);
        const rgb = isGold ? "239,177,30" : "216,25,142";

        if (glowStrength > 0.02) {
          const radius = 120 * machine.scale;
          const centerY = machine.y - (machine.type === "rack" ? 0 : 10 * machine.scale);
          const glow = ctx.createRadialGradient(machine.x, centerY, 0, machine.x, centerY, radius);
          glow.addColorStop(0, `rgba(${rgb},${0.32 * glowStrength})`);
          glow.addColorStop(0.5, `rgba(${rgb},${0.12 * glowStrength})`);
          glow.addColorStop(1, `rgba(${rgb},0)`);
          ctx.globalCompositeOperation = "lighter";
          ctx.fillStyle = glow;
          ctx.fillRect(machine.x - radius, centerY - radius, radius * 2, radius * 2);
          ctx.globalCompositeOperation = "source-over";
        }

        ctx.save();
        ctx.translate(machine.x, machine.y);
        ctx.scale(machine.scale, machine.scale);
        ctx.strokeStyle = glowStrength > 0.02 ? `rgba(${rgb},${0.2 + glowStrength * 0.8})` : "rgba(255,255,255,0.16)";
        ctx.lineWidth = (1.3 + glowStrength * 1.2) / machine.scale;
        ctx.shadowColor = `rgba(${rgb},${glowStrength})`;
        ctx.shadowBlur = glowStrength * 18;

        let screenX = null;
        let screenY = 0;
        let screenW = 0;
        let screenH = 0;

        if (machine.type === "mon") {
          this.roundedRect(-56, -40, 112, 70, 5);
          ctx.stroke();
          this.line(0, 30, 0, 44);
          this.line(-20, 44, 20, 44);
          screenX = -50;
          screenY = -34;
          screenW = 100;
          screenH = 58;
        } else if (machine.type === "lap") {
          this.roundedRect(-46, -40, 92, 58, 4);
          ctx.stroke();
          ctx.beginPath();
          ctx.moveTo(-50, 18);
          ctx.lineTo(50, 18);
          ctx.lineTo(60, 26);
          ctx.lineTo(-60, 26);
          ctx.closePath();
          ctx.stroke();
          screenX = -41;
          screenY = -35;
          screenW = 82;
          screenH = 48;
        } else {
          this.roundedRect(-28, -60, 56, 120, 4);
          ctx.stroke();
          for (let unit = 0; unit < 6; unit++) {
            const y = -52 + unit * 18;
            this.roundedRect(-22, y, 44, 13, 2);
            ctx.stroke();
            const on = Math.sin(machine.blink * (2 + unit * 1.3) * intensity + unit * 2) > 0.2 || glowStrength > 0.5;
            ctx.beginPath();
            ctx.arc(15, y + 6.5, 1.8, 0, TAU);
            ctx.fillStyle = on ? (unit % 3 === 0 ? "#EFB11E" : "#D8198E") : "rgba(255,255,255,0.12)";
            ctx.fill();
            for (let v = 0; v < 3; v++) {
              this.line(-16 + v * 5, y + 4, -16 + v * 5, y + 9);
            }
          }
        }

        if (screenX !== null) {
          ctx.shadowBlur = 0;
          ctx.fillStyle = `rgba(${rgb},${0.035 + glowStrength * 0.3})`;
          ctx.fillRect(screenX, screenY, screenW, screenH);
          ctx.save();
          ctx.beginPath();
          ctx.rect(screenX, screenY, screenW, screenH);
          ctx.clip();

          const lineHeight = 6;
          const totalHeight = machine.lines.length * lineHeight;
          machine.lines.forEach((lineDef, index) => {
            const y = screenY + 4 + (((index * lineHeight - machine.scroll) % totalHeight) + totalHeight) % totalHeight;
            if (y > screenY + screenH) return;
            ctx.fillStyle = lineDef.color;
            ctx.globalAlpha = 0.5 + glowStrength * 0.5;
            ctx.fillRect(screenX + 5 + lineDef.indent * 6, y, (screenW - 14 - lineDef.indent * 6) * lineDef.length, 2);
          });

          ctx.globalAlpha = 1;
          ctx.restore();
        }

        ctx.restore();
      }
    }

    drawRipples(delta) {
      const ctx = this.ctx;
      this.energy *= Math.pow(0.4, delta);
      ctx.globalCompositeOperation = "lighter";

      for (let index = this.ripples.length - 1; index >= 0; index--) {
        const ripple = this.ripples[index];
        ripple.age += delta;
        if (ripple.age > 1) {
          this.ripples.splice(index, 1);
          continue;
        }

        ctx.globalAlpha = (1 - ripple.age) * 0.6;
        ctx.beginPath();
        ctx.arc(ripple.x, ripple.y, 8 + ripple.age * 90, 0, TAU);
        ctx.strokeStyle = "#EFB11E";
        ctx.lineWidth = 1.5;
        ctx.stroke();
      }

      ctx.globalAlpha = 1;
      ctx.globalCompositeOperation = "source-over";
    }

    currentScreen() {
      const active = this.inner.querySelector("[data-screen-label]");
      return (active && active.getAttribute("data-screen-label") || "").toLowerCase();
    }

    drawDim(delta) {
      const screen = this.currentScreen();
      const heavyScreens = ["agenda", "session", "profile", "exhibitors", "sponsors", "emergence", "notifications", "attendees", "speakers", "communities", "checkout"];
      const lightScreens = ["login", "register", "start", "lobby", "networking", "reset password", "checkout result"];
      const target = heavyScreens.some((name) => screen.includes(name))
        ? 0.4
        : lightScreens.some((name) => screen.includes(name))
          ? 0.45
          : 1;

      this.dim += (target - this.dim) * Math.min(1, delta * 4);
      if (this.dim < 0.995) {
        this.ctx.globalCompositeOperation = "destination-out";
        this.ctx.fillStyle = `rgba(0,0,0,${1 - this.dim})`;
        this.ctx.fillRect(0, 0, this.width, this.height);
        this.ctx.globalCompositeOperation = "source-over";
      }
    }

    drawNodeGraph(delta, intensity) {
      const ctx = this.ctx;
      const stageRect = this.canvas.getBoundingClientRect();
      const nodes = {};
      const elements = this.inner.querySelectorAll("[data-node]");

      elements.forEach((element) => {
        const rect = element.getBoundingClientRect();
        nodes[element.dataset.node] = {
          x: rect.left - stageRect.left + rect.width / 2,
          y: rect.top - stageRect.top + rect.height / 2,
          r: rect.width / 2,
          w: rect.width,
          h: rect.height,
          rect: element.dataset.shape === "rect",
          element,
          hover: element.matches(":hover")
        };
      });

      const seen = new Set();
      for (const id in nodes) {
        const node = nodes[id];
        const targets = (node.element.dataset.to || "").split(/\s+/).filter(Boolean);

        for (const targetId of targets) {
          const target = nodes[targetId];
          if (!target) continue;

          const key = id + ">" + targetId;
          seen.add(key);

          let link = this.links.get(key);
          if (!link) {
            link = { pulses: [], next: Math.random() * 1.2 };
            this.links.set(key, link);
          }

          const mid = (target.x - node.x) * 0.5;
          const path = [node.x, node.y, node.x + mid, node.y, target.x - mid, target.y, target.x, target.y];
          const hot = node.hover || target.hover;

          ctx.beginPath();
          ctx.moveTo(path[0], path[1]);
          ctx.bezierCurveTo(path[2], path[3], path[4], path[5], path[6], path[7]);
          ctx.strokeStyle = hot ? "rgba(239,177,30,0.75)" : "rgba(255,255,255,0.22)";
          ctx.lineWidth = hot ? 2 : 1.25;
          ctx.stroke();

          link.next -= delta * intensity * (hot ? 2.6 : 1);
          if (link.next <= 0) {
            link.pulses.push({ t: 0, color: Math.random() < 0.35 ? "#EFB11E" : "#D8198E" });
            link.next = 1.1 + Math.random() * 1.4;
          }

          ctx.globalCompositeOperation = "lighter";
          for (let index = link.pulses.length - 1; index >= 0; index--) {
            const pulse = link.pulses[index];
            pulse.t += delta * (0.32 + 0.18 * intensity) * (hot ? 1.8 : 1);

            if (pulse.t >= 1) {
              if (hot) {
                this.rings.push({
                  x: target.x,
                  y: target.y,
                  r: target.r,
                  w: target.w,
                  h: target.h,
                  rect: target.rect,
                  age: 0,
                  color: "#EFB11E"
                });
              }
              link.pulses.splice(index, 1);
              continue;
            }

            const eased = Math.pow(pulse.t, 1.7);
            for (let trail = 0; trail < 10; trail++) {
              const point = this.bezier(path, Math.max(0, eased - trail * 0.012));
              ctx.globalAlpha = (1 - trail / 10) * 0.9;
              ctx.beginPath();
              ctx.arc(point.x, point.y, 3.2 - trail * 0.22, 0, TAU);
              ctx.fillStyle = hot ? "#EFB11E" : pulse.color;
              ctx.fill();
            }
          }
          ctx.globalAlpha = 1;
          ctx.globalCompositeOperation = "source-over";
        }
      }

      for (const key of this.links.keys()) {
        if (!seen.has(key)) this.links.delete(key);
      }

      ctx.globalCompositeOperation = "lighter";
      for (let index = this.rings.length - 1; index >= 0; index--) {
        const ring = this.rings[index];
        ring.age += delta;
        if (ring.age > 0.9) {
          this.rings.splice(index, 1);
          continue;
        }

        ctx.globalAlpha = (1 - ring.age / 0.9) * 0.7;
        ctx.beginPath();
        const grow = 4 + ring.age * 30;
        if (ring.rect && ctx.roundRect) {
          ctx.roundRect(ring.x - ring.w / 2 - grow, ring.y - ring.h / 2 - grow, ring.w + grow * 2, ring.h + grow * 2, 26 + grow);
        } else {
          ctx.arc(ring.x, ring.y, ring.r + grow, 0, TAU);
        }
        ctx.strokeStyle = ring.color;
        ctx.lineWidth = 1.5;
        ctx.stroke();
      }
      ctx.globalAlpha = 1;
      ctx.globalCompositeOperation = "source-over";
    }

    frame(now) {
      this.raf = requestAnimationFrame(this.frame);
      const delta = Math.min(0.05, (now - this.last) / 1000);
      const intensity = this.intensity();
      this.last = now;

      this.ctx.clearRect(0, 0, this.width, this.height);
      this.drawCablePulses(delta, intensity);
      this.drawMachines(delta, intensity);
      this.drawRipples(delta);
      this.drawDim(delta);
      this.drawNodeGraph(delta, intensity);
    }

    destroy() {
      cancelAnimationFrame(this.raf);
      this.resizeObserver.disconnect();
      window.removeEventListener("pointermove", this.onMove);
      window.removeEventListener("pointerdown", this.onDown);
      document.documentElement.removeEventListener("pointerleave", this.onLeave);
      this.canvas.remove();
    }
  }

  function boot() {
    document.querySelectorAll(".rc-stage").forEach((stage) => {
      if (stage.__accelerateBackground) return;
      stage.__accelerateBackground = new AccelerateBackground(stage);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
