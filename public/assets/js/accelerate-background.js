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

  const REFERENCE_HOLOGRAM_GEAR = (() => {
    let path = "";
    for (let index = 0; index < 32; index++) {
      const angle = index / 32 * TAU - TAU / 64;
      const radius = index % 4 < 2 ? 9.6 : 7.3;
      path += (index ? "L" : "M") + (12 + Math.cos(angle) * radius).toFixed(2) + " " + (12 + Math.sin(angle) * radius).toFixed(2);
    }
    return path + "Z M15 12a3 3 0 1 0-6 0a3 3 0 1 0 6 0";
  })();
  const REFERENCE_HOLOGRAM_ICON_PATHS = [
    'M19.5 13.5c1.4-1.4 2.5-3 2.5-5A5 5 0 0 0 17 3.5c-1.8 0-3.2.8-5 2.6C10.2 4.3 8.8 3.5 7 3.5a5 5 0 0 0-5 5c0 2 1.1 3.6 2.5 5L12 21z M3 12h5l1.5-2.5 2.5 5 2-6 1.5 3.5H21',
    'M12 4 2 9l10 5 10-5z M6 11v5c2.5 2.5 9.5 2.5 12 0v-5 M22 9v6',
    'M6.5 3.2a2.3 2.3 0 1 0 .01 0z M17.5 3.2a2.3 2.3 0 1 0 .01 0z M2.5 20v-5.5a4 4 0 0 1 8 0v1 M13.5 15.5v-1a4 4 0 0 1 8 0V20 M12 10.3a1.7 1.7 0 1 0 .01 0z M9.5 20.5v-2a2.5 2.5 0 0 1 5 0v2',
    'M12 21v-8.5 M6.5 21h11 M12 13.5C8 13.5 5.5 11 5.5 7c4 0 6.5 2.5 6.5 6.5z M12 11.5c0-4.2 2.6-7 6.8-7 0 4.2-2.6 7-6.8 7z',
    'M4.5 5.5h15v10h-15z M2 19.5h20l-2-4h-16z M9.6 13h4.9a2 2 0 0 0 .2-4 2.8 2.8 0 0 0-5.3.7A1.7 1.7 0 0 0 9.6 13z',
    'M5 6a7 2.6 0 1 0 14 0a7 2.6 0 1 0-14 0 M5 6v3.5c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V6 M5 9.5V13c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V9.5 M5 13v3.5c0 1.4 3.1 2.6 7 2.6s7-1.2 7-2.6V13',
    'M3 20.5h18 M5 20.5v-5h3v5 M10.5 20.5v-8h3v8 M16 20.5v-11h3v11 M4 11.5l5-4.5 3.5 2.5 7-6 M15.5 3.5h4v4',
    'M3 21h18 M4.5 18h15 M6 18v-7 M10 18v-7 M14 18v-7 M18 18v-7 M3.5 11h17L12 4z',
    'M12 3v17.5 M7.5 20.5h9 M4 7h16 M4 7l-2.5 7c0 1.6 5 1.6 5 0z M20 7l-2.5 7c0 1.6 5 1.6 5 0z',
    REFERENCE_HOLOGRAM_GEAR,
    'M9 18h6 M10 21.5h4 M9 14.5V18 M15 14.5V18 M15 14.5c.2-1.1.8-2 1.6-2.8A6 6 0 1 0 7.4 11.7c.8.8 1.4 1.7 1.6 2.8',
    'M12 9.5a5.5 5.5 0 1 0 0.01 0z M6.5 15h11 M12 9.5c-1.7 1.5-2.5 3.4-2.5 5.5s.8 4 2.5 5.5c1.7-1.5 2.5-3.4 2.5-5.5s-.8-4-2.5-5.5z M12 1.6a1.5 1.5 0 1 0 .01 0z M9.6 7.6a2.4 2.4 0 0 1 4.8 0 M4.3 6.3a1.5 1.5 0 1 0 .01 0z M1.9 12.4a2.4 2.4 0 0 1 4.8 0 M19.7 6.3a1.5 1.5 0 1 0 .01 0z M17.3 12.4a2.4 2.4 0 0 1 4.8 0'
  ];


  /*
   * The portal's default scene.  It deliberately lives behind the UI rather
   * than being tied to a particular page, so the visual language remains
   * continuous as an attendee moves through the event.
   */
  class HologramBackground {
    constructor(stage) {
      this.stage = stage;
      this.canvas = document.createElement("canvas");
      this.canvas.className = "rc-bg-canvas";
      this.stage.prepend(this.canvas);
      this.ctx = this.canvas.getContext("2d");
      this.last = performance.now();
      this.time = 0;
      this.reduced = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      this.resize = this.resize.bind(this);
      this.frame = this.frame.bind(this);
      this.observer = new ResizeObserver(this.resize);
      this.observer.observe(this.canvas);
      this.resize();
      this.raf = requestAnimationFrame(this.frame);
    }

    resize() {
      const dpr = Math.min(2, window.devicePixelRatio || 1);
      this.width = Math.max(1, this.canvas.clientWidth);
      this.height = Math.max(1, this.canvas.clientHeight);
      this.canvas.width = Math.round(this.width * dpr);
      this.canvas.height = Math.round(this.height * dpr);
      this.ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      this.buildScene();
    }

    buildScene() {
      const W = this.width;
      const H = this.height;
      const mapWidth = Math.min(W * .8, H * .62 * 360 / 142);
      const mapHeight = mapWidth * 142 / 360;
      const sectorRadius = Math.max(16, Math.min(32, mapWidth * .027));
      const sectorCurveX = mapWidth * .55;
      const sectorCurveY = mapHeight / 2 + mapWidth * .08;
      this.map = {
        x: W / 2,
        y: Math.max(H * .47, 72 + sectorRadius + sectorCurveY),
        w: mapWidth,
        h: mapHeight
      };
      const map = this.map;
      const point = (lng, lat) => [map.x + lng / 180 * map.w / 2, map.y - lat / 90 * map.h / 2];
      const random = this.seeded(20261015 + Math.round(W) * 7 + Math.round(H));
      this.dots = [];
      const spacing = Math.max(3.2, Math.min(6, map.w / 230));
      const mapLeft = map.x - map.w / 2;
      const mapTop = map.y - map.h / 2;
      for (let row = 0, y = mapTop + spacing * .5; y < mapTop + map.h; y += spacing * .88, row++) {
        const lat = 84 - (y - mapTop) / map.h * 142;
        for (let x = mapLeft + (row & 1 ? spacing * .75 : spacing * .25); x < mapLeft + map.w; x += spacing) {
          const lng = (x - mapLeft) / map.w * 360 - 180;
          if (!this.onLand(lng, lat)) continue;
          const coast = !this.onLand(lng + 1.4, lat) || !this.onLand(lng - 1.4, lat) || !this.onLand(lng, lat + 1.4) || !this.onLand(lng, lat - 1.4);
          const toneSeed = Math.max(0, Math.min(1, .45 + .3 * Math.sin(lng * .021 + .4) + .22 * Math.sin(lat * .055 - lng * .012 + 2.1) + (random() - .5) * .4));
          const tone = toneSeed < .5 ? 2 * toneSeed * toneSeed : 1 - 2 * (1 - toneSeed) * (1 - toneSeed);
          this.dots.push({
            x: x + (random() - .5) * (random() < .12 ? spacing * .35 : 0),
            y: y + (random() - .5) * (random() < .12 ? spacing * .35 : 0),
            r: (coast ? 1.45 : 1.15) * Math.min(1.4, spacing / 4),
            gold: tone < .52,
            a: (coast ? .42 : .2) + random() * .22,
            phase: random() * TAU
          });
        }
      }

      const cityData = [
        ["New York", -74, 40.7], ["São Paulo", -46.6, -23.5], ["London", -0.1, 51.5],
        ["Lagos", 3.4, 6.5], ["Cairo", 31.2, 30], ["Johannesburg", 28, -26.2],
        ["Nairobi", 36.8, -1.3], ["Dubai", 55.3, 25.2], ["Mumbai", 72.9, 19.1],
        ["Singapore", 103.8, 1.3], ["Beijing", 116.4, 39.9], ["Tokyo", 139.7, 35.7], ["Sydney", 151.2, -33.9]
      ];
      this.cities = cityData.map(([name, lng, lat], index) => {
        const p = point(lng, lat);
        return { name, x: p[0], y: p[1], phase: index * .81 + random() };
      });
      const cityLinks = [[0,2],[0,1],[0,3],[1,3],[1,5],[2,3],[2,4],[2,7],[2,10],[3,4],[3,6],[3,5],[3,7],[4,7],[4,6],[6,5],[6,8],[7,8],[7,9],[8,9],[8,10],[9,10],[9,12],[10,11],[11,12],[5,12]];
      this.cityLinks = cityLinks.map(([from, to], index) => ({ from, to, phase: index * .47 + random() * 2 }));
      this.cityPulses = Array.from({ length: 9 }, () => ({
        link: Math.floor(random() * this.cityLinks.length),
        progress: random() * .7
      }));
      this.nextCityPulse = .3;

      const sectors = ["Healthcare", "Education", "Finance", "Agriculture", "Technology", "Energy", "Trade", "Manufacturing", "Mobility", "Creative", "Public Sector", "Climate"];
      this.sectors = sectors.map((name, index) => {
        const angle = (200 - 220 * index / 11) * Math.PI / 180;
        const x = map.x + sectorCurveX * Math.sign(Math.cos(angle)) * Math.pow(Math.abs(Math.cos(angle)), .62);
        const y = map.y - sectorCurveY * Math.sign(Math.sin(angle)) * Math.pow(Math.abs(Math.sin(angle)), .62);
        return { name, x, y, r: sectorRadius, color: index % 2 ? "206,92,224" : "255,182,70", phase: index * .63 };
      });
      this.sectorLinks = this.sectors.map((sector, index) => ({
        sector: index,
        city: (index * 5 + 3) % this.cities.length,
        phase: index * .71
      }));
      this.sectorPulses = [];
      this.nextSectorPulse = 1;
      this.lastSectorPulse = -1;
      this.floorY = Math.max(mapTop + map.h + 28, H * .775);
      this.floorRings = Array.from({ length: 5 }, (_, index) => ({
        rx: map.w * (.13 + index * .095),
        ry: map.w * (.016 + index * .012),
        phase: index * .9,
        violet: index % 2 === 1
      }));
      this.particles = Array.from({ length: 52 }, () => ({
        x: random() * W, y: random() * H, speed: 4 + random() * 13,
        size: .7 + random() * 1.4, alpha: .09 + random() * .2, violet: random() > .5
      }));
    }

    seeded(seed) {
      let value = seed >>> 0;
      return () => {
        value = (value * 1664525 + 1013904223) >>> 0;
        return value / 4294967296;
      };
    }

    onLand(lng, lat) {
      // Exact land mask recovered from the supplied RC design. It is a packed
      // 360 × 142 bit map, so the point field follows the same coastlines.
      if (!this.landMask) {
        const encoded = 'AAAAAAAAAAAAAAAAAAAAAADw/wcAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADg//8/AOf//38AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD+///v////////AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPj///////////8fAAAAwD8AAHwAAAAAAH8AAAAAAAAAAAAAAAAAAAAAAAAAAPD//w/+//////8DAADA/38AAAAAAAAAAP4GAAAAAAAAAAAAAAAAAAAAAAA4+M7///n///////8BAACA/wYAAAAAAAAAAIAfAAAAAAAAAAAAAAAAAAAAAPA4ADj4P/D///////8BAAAAPA4AAAAAAAAAAAA8AAAAAAAAAAAAAAAAAAAAAP7H4Pv9P+D///////8BAAAAAAAAAAAA/gEAAMD/HwAAAAAAAAAAAAAAAAAAAMD/w7v/DwAA//////8BAAAAAAAAAADwHwAA8P//PwAA4N8BAAAAAAAAAAAAgA88APD/DwAA/P////8AAAAAAAAAAAD4AAAA/P//DwAAwAACAAAAAAAAAAAAgP8Bhve5fwAA8P////8AAAAAAAAAAAA+AADw/////4cPAA8AAAAAAAAAAAAAwP//53/8/wEA4P///z8AAAAAAAAAAAAPAN78//////8fgP8BAAAAAwCAAAAAwO//D3/8/w8A8P///z8AAAAAAAAAAIAPgP////////8///8PAACAAAD8/wAAEMD/P/j4//8A4P///z8AAAAAAPgHAAAcgN//////////////DwAAAAD////z/5//f//Bx/8B4P///x8AAAAAwP8/AAAAn////////////////99/A+D///////+/H+7Ph/8BgP///wMAAAAA8P//B4P/////////////////////H4D/////////////h/kPwP//BwAAAAAA/P//H/f//9///////////////////8P/////////////A/w/wP//AzAYAAAA/v////////////////////////////P/////////////wP8dgP8/APA/AACA//d/8P//////////////////////4OD/////////////4/8HAP8PAPA/AADA//v/////////////////////////AAf+///////////jDvAPAP8HAIAHAADw//z///////////////////////9/AMD///////////8A0+cHAP4DAAAAAAD+P/7/////////////////////////AMD//////////38AwB8AAPwDAAAAAAD+P/7/////////////////////f/8HAOD///7//////z8AwH8AAMABAAAAAAD+f/7/////////////////////8P8AAAD8d4D//////z8AwH8cAAAAAAAAAAD+f/j///////////////////9/8AAAAAD0BwD8/////38AwP8/AAAAAAAAwAGcH/j//////////////////wMAeAAAAADADwDA//////8AgP8/AAAAAAAAwAPgH/b//////////////////wAAfwAAAABwAACA//////8PgP9/AAAAAAAAwANwD/7/////////////////fwAAfwAAAAAeAAAA////////wP//AQAAAAAAwAewA/7/////////////////HwCAPwAAAIABAAAA/v//////8///BwAAAAAAuA/w+f//////////////////HwCAPwAAAAAAAACA/P//////4///DwAAAAAAvA/+/////////////////////wcADwAAAAAAAAAA+f//////5///HwAAAAAAvD///////////////////////wcABwAAAAAAAAAA8P//////////HwAAAAAAiL///////////////////////w8AAwAAAAAAAAAA+P//////////DQAAAAAAwOf//////////////////////w0AAAAAAAAAAAAA4P////////8xPAAAAAAAAPz//////////////////////x0AAAAAAAAAAAAAgP////////8PfgAAAAAAgP///////////////////////wQAAAAAAAAAAAAAgP////////8H/gAAAAAAAP//////////////////////fwQAAAAAAAAAAAAAAP////////+/QAAAAAAAAPz//////f//////////////fwwAAAAAAAAAAAAAAP//////////AAAAAAAAAPj////j////////////////PwAAAAAAAAAAAAAAAP////////8dAAAAAAAAAPz/+f9h/v//////////////Hw4AAAAAAAAAAAAAgP///////z8EAAAAAAAA+P/P8/8B+P//////////////Bz8AAAAAAAAAAAAAgP///////z8AAAAAAAAA+H+wx/8A4P////////////9/AA8AAAAAAAAAAAAAAP///////z8AAAAAAAAA+H8gn//54f////////////8/AAMAAAAAAAAAAAAAgP///////wcAAAAAAAAA+B8wvN////////////////8fAAMAAAAAAAAAAAAAAP///////wMAAAAAAAAA/A8wGM///////////////+8PAAMAAAAAAAAAAAAAAP///////wEAAAAAAAAA/A8AGM7//////////////wMegAMAAAAAAAAAAAAAAP7//////wAAAAAAAAAA+AcgD47//////////////z8ewAEAAAAAAAAAAAAAAPz//////wAAAAAAAAAA4PN/AAT//////////////z888AEAAAAAAAAAAAAAAPj//////wAAAAAAAAAAwP1/ADAg/////////////w88/wEAAAAAAAAAAAAAAPj/////fwAAAAAAAAAA4P9/AAAw/////////////w+MfwAAAAAAAAAAAAAAAMD/////PwAAAAAAAAAA+P9/AACA/////////////x/ADwAAAAAAAAAAAAAAAID/////DwAAAAAAAAAA/P//Bw+A/////////////z/AAAAAAAAAAAAAAAAAAID/////BwAAAAAAAAAA/P//D//M/////////////z/AAAAAAAAAAAAAAAAAAAD7////BwAAAAAAAAAA/P///////////////////z8AAAAAAAAAAAAAAAAAAAD7//8HBwAAAAAAAAAA/P///////9///////////z8AAAAAAAAAAAAAAAAAAAD2/x8ADgAAAAAAAAAA/////////5///////////z8AAAAAAAAAAAAAAAAAAADu/wcADgAAAAAAAADA//////+//x///////////x8AAAAAAAAAAAAAAAAAAADc/wcAbAAAAAAAAADA//////8//z/k/////////w8AAAAAAAAAAAAAAAAAAACQ/wcADAAAAAAAAADg//////9//v/4/////////w8AAAAAAAAAAAAAAAAAAAAw/wcAYAAAAAAAAADw/////////v8cgP///////zcAAAAAAAAAAAAAAAAAAABg/gMAAAAAAAAAAADw/////////P9/AP///////zMAAAAAAAAAAAAAAAAAAAAA/AOAPwAAAAAAAAD4////////+f//AP7//////xAAAAAAAAAAAABAAAAAAAAA/Acc8AAAAAAAAAD4////////+f//AP7/H///HwAAAAAAAAAAAACAAAAAAAAA/AcewAMAAAAAAAD4////////+f9/AOD/B/9/BAAAAAAAAAAAAAAAAQAAAAAA+A8eAHgAAAAAAAD4////////4/8/AOD/A/4/BwAAAAAAAAAAAAAAAAAAAAAA8P8PQP4CAAAAAADw////////4/8fAOD/APw/AyAAAAAAAAAAAAAAAAAAAAAAwP8PAAAAAAAAAAD4////////x/8PAOB/APx/AHAAAAAAAAAAAAAAAAAAAAAAAP8PAAAAAAAAAAD4////////x/8DAOA/APz/AHAAAAAAAAAAAAAAAAAAAAAAAMj/AQAAAAAAAAD4////////z/8AAMAfAOD/ATAAAAAAAAAAAAAAAAAAAAAAAID/AQAAAAAAAAD8////////vz8AAMAPAMD/AXAAAAAAAAAAAAAAAAAAAAAAAAD8AQAAAAAAAAD4////////vw8AAMAPAMD/A/AAAAAAAAAAAAAAAAAAAAAAAADwARAAAAAAAAD4/////////wEAAIAPAMD+AyADAAAAAAAAAAAAAAAAAAAAAADgAX4AAAAAAADw/////////3AAAIAPAMD4AUADAAAAAAAAAAAAAAAAAAAAAADAAf9/AAAAAADg/////////38AAAAPAMD4AMgBAAAAAAAAAAAAAAAAAAAAAACAM/9/AAAAAADA/////////38AAAAXAMBgAMQCAAAAAAAAAAAAAAAAAAAAAAAA7///AQAAAACA/////////38AAAA6AMABAIIHAAAAAAAAAAAAAAAAAAAAAAAAzP//AwAAAACA/////////z8AAAAwAIABAMAHAAAAAAAAAAAAAAAAAAAAAAAAwP//BwAAAAAA/////////z8AAAAwAAADAAMHAAAAAAAAAAAAAAAAAAAAAAAAwP//fwAAAAAA/B/+/////x8AAAAAABgHgAcAAAAAAAAAAAAAAAAAAAAAAAAAwP///wEAAAAAOAD+/////w8AAAAAAHgPwAcAAAAAAAAAAAAAAAAAAAAAAAAAwP///wEAAAAAAADg/////wcAAAAAAPAO4AMAAAAAAAAAAAAAAAAAAAAAAAAA4P///wMAAAAAAADg/////wcAAAAAAOAP+AMAAAAAAAAAAAAAAAAAAAAAAAAA4P///wMAAAAAAADg/////wEAAAAAAMAP/gcZAAAAAAAAAAAAAAAAAAAAAAAA8P///wMAAAAAAADg/////wAAAAAAAIAP/vMZAAAAAAAAAAAAAAAAAAAAAAAA+P///x8AAAAAAADg////fwAAAAAAAAAP/luQAwAAAAAAAAAAAAAAAAAAAAAA+P///38AAAAAAADg////PwAAAAAAAAAf/nmAYwAAAAAAAAAAAAAAAAAAAAAA+P////8DAAAAAADg////HwAAAAAAAAA+/DkA9wMAAAAAAAAAAAAAAAAAAAAA+P////8/AAAAAADA////DwAAAAAAAAA88Hl0/w8QAAAAAAAAAAAAAAAAAAAA/P////9/AAAAAACA////DwAAAAAAAAA4AHgA+D8YAAAAAAAAAAAAAAAAAAAA+P//////AQAAAAAA////BwAAAAAAAAAwAFgAwH9PAAAAAAAAAAAAAAAAAAAA+P//////AQAAAAAA////BwAAAAAAAADgDQAAxP+GAAAAAAAAAAAAAAAAAAAA8P//////AQAAAAAA/v//DwAAAAAAAADAPwAAwP8ABAAAAAAAAAAAAAAAAAAA4P//////AQAAAAAA/v//DwAAAAAAAAAA+HYGQM8BGAAAAAAAAAAAAAAAAAAA4P//////AQAAAAAA/v//DwAAAAAAAAAAABgDAIQDOAAAAAAAAAAAAAAAAAAAwP//////AAAAAAAA/v//HwAAAAAAAAAAAAAAAAAGIAAAAAAAAAAAAAAAAAAAwP////9/AAAAAAAA/v//HwAAAAAAAAAAAAAAAwQAAAAAAAAAAAAAAAAAAAAAgP////8/AAAAAAAA/v//HyAAAAAAAAAAAADAHw4AAAAAAAAAAAAAAAAAAAAAgP////8/AAAAAAAA////HzAAAAAAAAAAAADADw4AAAAAAAAAAAAAAAAAAAAAAP////8fAAAAAAAA////H3gAAAAAAAAAAAD+Dx4AAAAAAAAAAAAAAAAAAAAAAP7///8fAAAAAAAA////H3wAAAAAAAAAAAD/Hz4AAAQAAAAAAAAAAAAAAAAAAPz///8fAAAAAACA////Dz8AAAAAAAAAAID/fz4AAAiAAAAAAAAAAAAAAAAAAPD///8fAAAAAACA////Az8AAAAAAAAAAMD//z8AAABgAAAAAAAAAAAAAAAAAOD///8fAAAAAAAA////AT8AAAAAAAAAAMD//38AAAAAAAAAAAAAAAAAAAAAAMD///8fAAAAAAAA////AB8AAAAAAAAAAPD///8AAAAAAAAAAAAAAAAAAAAAAMD///8PAAAAAAAA/v9/AB8AAAAAAAAAAP////8BAAEAAAAAAAAAAAAAAAAAAMD///8HAAAAAAAA/v//gB8AAAAAAAAAgP////8DAAYAAAAAAAAAAAAAAAAAAOD///8HAAAAAAAA/P//gA8AAAAAAAAAwP////8HAAQAAAAAAAAAAAAAAAAAAOD///8AAAAAAAAA/P//gA8AAAAAAAAA4P////8HAAAAAAAAAAAAAAAAAAAAAOD//x8AAAAAAAAA/P9/AA8AAAAAAAAA4P////8PAAAAAAAAAAAAAAAAAAAAAOD//w8AAAAAAAAA/P8fAAIAAAAAAAAAwP////8fAAAAAAAAAAAAAAAAAAAAAOD//w8AAAAAAAAA+P8fAAAAAAAAAAAA4P////8fAAAAAAAAAAAAAAAAAAAAAOD//w8AAAAAAAAA+P8fAAAAAAAAAAAAwP////8/AAAAAAAAAAAAAAAAAAAAAPD//wcAAAAAAAAA8P8fAAAAAAAAAAAAwP////8/AAAAAAAAAAAAAAAAAAAAAPD//wcAAAAAAAAA4P8PAAAAAAAAAAAAgP////8/AAAAAAAAAAAAAAAAAAAAAPD//wMAAAAAAAAA4P8HAAAAAAAAAAAAgP////8fAAAAAAAAAAAAAAAAAAAAAPD//wEAAAAAAAAAwP8DAAAAAAAAAAAAgP////8fAAAAAAAAAAAAAAAAAAAAAPD//wAAAAAAAAAAwP8BAAAAAAAAAAAAgP8D/v8fAAAAAAAAAAAAAAAAAAAAAPD/fwAAAAAAAAAAwH8AAAAAAAAAAAAAgP8A/P8PAAAAAAAAAAAAAAAAAAAAAPD/PwAAAAAAAAAAgAMAAAAAAAAAAAAAgA8A6P8HAAAAAAAAAAAAAAAAAAAAAPj/BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAwP8HAAAGAAAAAAAAAAAAAAAAAPj/BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAgP8DAAAMAAAAAAAAAAAAAAAAAPz/BwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8DAAA8AAAAAAAAAAAAAAAAAPz/AwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH4AAAB8AAAAAAAAAAAAAAAAAPw/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAcAAAAAAAAAAAAAAAAAPw/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAZAAAAAAAAAAAAAAAAAPwHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOABAAAPAAAAAAAAAAAAAAAAAPwfAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAOAAAIADAAAAAAAAAAAAAAAAAPgHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAEAAAOABAAAAAAAAAAAAAAAAAP4HAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPgAAAAAAAAAAAAAAAAAAP4DAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHwAAAAAAAAAAAAAAAAAAP8BAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADgAAAAAAAAAAAAAAAAAAP4HAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8DAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP8BAAAAAAAAAAAAAAAAAAAAAAYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH6AAwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP4AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPgHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMABAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA';
        const raw = atob(encoded);
        this.landMask = Uint8Array.from(raw, char => char.charCodeAt(0));
      }
      const row = Math.floor(84 - lat);
      if (row < 0 || row >= 142) return false;
      const col = ((Math.floor(lng + 180) % 360) + 360) % 360;
      const index = row * 360 + col;
      return Boolean((this.landMask[index >> 3] >> (index & 7)) & 1);
    }

    curve(a, b, lift = .16) {
      const dx = b.x - a.x;
      const dy = b.y - a.y;
      return [a.x, a.y, a.x + dx * .35, a.y - Math.abs(dx) * lift - dy * .08, b.x - dx * .35, b.y - Math.abs(dx) * lift + dy * .08, b.x, b.y];
    }

    at(path, t) {
      const u = 1 - t;
      return {
        x: u ** 3 * path[0] + 3 * u * u * t * path[2] + 3 * u * t * t * path[4] + t ** 3 * path[6],
        y: u ** 3 * path[1] + 3 * u * u * t * path[3] + 3 * u * t * t * path[5] + t ** 3 * path[7]
      };
    }

    strokeCurve(path, color, alpha, width = 1) {
      const ctx = this.ctx;
      ctx.beginPath();
      ctx.moveTo(path[0], path[1]);
      ctx.bezierCurveTo(path[2], path[3], path[4], path[5], path[6], path[7]);
      ctx.strokeStyle = `rgba(${color},${alpha})`;
      ctx.lineWidth = width;
      ctx.stroke();
    }

    drawPulse(path, progress, color) {
      const ctx = this.ctx;
      for (let trail = 0; trail < 9; trail++) {
        const p = this.at(path, Math.max(0, progress - trail * .018));
        ctx.globalAlpha = (1 - trail / 9) * .8;
        ctx.beginPath();
        ctx.arc(p.x, p.y, 2.8 - trail * .22, 0, TAU);
        ctx.fillStyle = `rgb(${color})`;
        ctx.fill();
      }
      ctx.globalAlpha = 1;
    }

    drawScene(delta) {
      const ctx = this.ctx;
      const W = this.width;
      const H = this.height;
      const t = this.time;
      const map = this.map;
      const background = ctx.createRadialGradient(map.x, map.y, 0, map.x, map.y, Math.max(W, H) * .76);
      background.addColorStop(0, "rgb(50,16,76)");
      background.addColorStop(.45, "rgb(24,7,40)");
      background.addColorStop(1, "rgb(5,1,10)");
      ctx.fillStyle = background;
      ctx.fillRect(0, 0, W, H);

      ctx.globalCompositeOperation = "lighter";
      [[W * .08, H * .38, W * .42, "255,159,59"], [W * .92, H * .3, W * .46, "187,76,255"], [map.x, H * .72, map.w * .7, "235,65,186"]].forEach(([x, y, r, color]) => {
        const glow = ctx.createRadialGradient(x, y, 0, x, y, r);
        glow.addColorStop(0, `rgba(${color},.13)`);
        glow.addColorStop(1, `rgba(${color},0)`);
        ctx.fillStyle = glow;
        ctx.fillRect(x - r, y - r, r * 2, r * 2);
      });

      // Soft edge streaks frame the scene without competing with content.
      [[0, H * .27, 1, "255,192,80"], [0, H * .61, -1, "223,105,255"], [W, H * .22, -1, "229,117,255"], [W, H * .65, 1, "255,187,71"]].forEach(([x, y, tilt, color], index) => {
        const length = W * (.28 + (index % 2) * .08);
        const gradient = ctx.createLinearGradient(x, y, x + (x ? -length : length), y + tilt * H * .07);
        gradient.addColorStop(0, `rgba(${color},.34)`);
        gradient.addColorStop(1, `rgba(${color},0)`);
        ctx.save();
        ctx.translate(x, y + Math.sin(t * .4 + index) * 18);
        ctx.rotate(tilt * .11);
        ctx.fillStyle = gradient;
        ctx.fillRect(x ? -length : 0, -2, length, 4);
        ctx.restore();
      });

      // The gold-and-violet point cloud makes the world map.
      this.dots.forEach(dot => {
        const pulse = .75 + Math.sin(t * .75 + dot.phase) * .25;
        ctx.globalAlpha = dot.a * pulse;
        ctx.fillStyle = dot.gold ? "#ffb646" : "#ce5ce0";
        ctx.fillRect(dot.x - dot.r / 2, dot.y - dot.r / 2, dot.r, dot.r);
      });
      ctx.globalAlpha = 1;

      this.cityLinks.forEach(link => {
        const path = this.curve(this.cities[link.from], this.cities[link.to]);
        this.strokeCurve(path, link.from % 3 ? "247,194,79" : "193,105,244", .18, 1);
      });

      this.nextCityPulse -= delta;
      if (this.nextCityPulse <= 0 && this.cityPulses.length < 13) {
        this.cityPulses.push({ link: Math.floor(Math.random() * this.cityLinks.length), progress: 0 });
        this.nextCityPulse = .3 + Math.random() * .4;
      }
      this.cityPulses = this.cityPulses.filter(pulse => {
        pulse.progress += delta * .13;
        if (pulse.progress >= 1.1) return false;
        const link = this.cityLinks[pulse.link];
        const path = this.curve(this.cities[link.from], this.cities[link.to]);
        this.drawPulse(path, Math.min(1, pulse.progress), pulse.link % 3 ? "255,182,70" : "206,92,224");
        return true;
      });

      this.sectorLinks.forEach(link => {
        const sector = this.sectors[link.sector];
        const city = this.cities[link.city];
        const path = this.curve(sector, city, .09);
        this.strokeCurve(path, sector.color, .17, .9);
      });

      this.nextSectorPulse -= delta;
      if (this.nextSectorPulse <= 0 && this.sectorPulses.length < 8) {
        let sector;
        do sector = Math.floor(Math.random() * this.sectorLinks.length); while (sector === this.lastSectorPulse);
        this.lastSectorPulse = sector;
        this.sectorPulses.push({ sector, progress: 0 });
        this.nextSectorPulse = .4 + Math.random() * .5;
      }
      this.sectorPulses = this.sectorPulses.filter(pulse => {
        pulse.progress += delta * .15;
        if (pulse.progress >= 1.1) return false;
        const link = this.sectorLinks[pulse.sector];
        const path = this.curve(this.sectors[link.sector], this.cities[link.city], .09);
        this.drawPulse(path, Math.min(1, pulse.progress), this.sectors[link.sector].color);
        return true;
      });

      this.cities.forEach(city => {
        const pulse = .5 + .5 * Math.sin(t * 1.35 + city.phase);
        const radius = 8 + pulse * 8;
        const glow = ctx.createRadialGradient(city.x, city.y, 0, city.x, city.y, radius);
        glow.addColorStop(0, "rgba(255,245,215,.96)");
        glow.addColorStop(.22, "rgba(255,192,70,.76)");
        glow.addColorStop(1, "rgba(222,100,255,0)");
        ctx.fillStyle = glow;
        ctx.fillRect(city.x - radius, city.y - radius, radius * 2, radius * 2);
      });

      // Rotating sector rims and breathing sector centres.
      this.sectors.forEach((sector, index) => {
        const breath = .5 + .5 * Math.sin(t * 1.15 + sector.phase);
        const halo = ctx.createRadialGradient(sector.x, sector.y, 0, sector.x, sector.y, sector.r * (2.1 + breath * .45));
        halo.addColorStop(0, `rgba(${sector.color},${.21 + breath * .12})`);
        halo.addColorStop(1, `rgba(${sector.color},0)`);
        ctx.fillStyle = halo;
        ctx.fillRect(sector.x - sector.r * 2.6, sector.y - sector.r * 2.6, sector.r * 5.2, sector.r * 5.2);
        ctx.beginPath();
        ctx.arc(sector.x, sector.y, sector.r, 0, TAU);
        ctx.fillStyle = "rgba(20,7,38,.78)";
        ctx.fill();
        ctx.strokeStyle = `rgba(${sector.color},.58)`;
        ctx.lineWidth = 1;
        ctx.stroke();
        const rotation = (t * (index % 2 ? -.55 : .55) + sector.phase) % TAU;
        ctx.beginPath();
        ctx.arc(sector.x, sector.y, sector.r * 1.23, rotation, rotation + 1.36);
        ctx.strokeStyle = `rgba(${sector.color},.9)`;
        ctx.lineWidth = 1.4;
        ctx.stroke();
        ctx.beginPath();
        ctx.arc(sector.x, sector.y, sector.r * 1.44, rotation + Math.PI, rotation + Math.PI + .7);
        ctx.strokeStyle = "rgba(255,255,255,.45)";
        ctx.lineWidth = .8;
        ctx.stroke();
        ctx.globalCompositeOperation = "source-over";
        ctx.save();
        ctx.translate(sector.x, sector.y);
        const iconScale = sector.r * 1.12 / 24;
        ctx.scale(iconScale, iconScale);
        ctx.translate(-12, -12.2);
        const icon = new Path2D(REFERENCE_HOLOGRAM_ICON_PATHS[index]);
        ctx.lineWidth = 1.55;
        ctx.lineCap = "round";
        ctx.lineJoin = "round";
        ctx.strokeStyle = "rgba(255,208,128,.95)";
        ctx.shadowColor = "rgba(255,160,60,.85)";
        ctx.shadowBlur = 5;
        ctx.stroke(icon);
        ctx.shadowBlur = 0;
        ctx.stroke(icon);
        ctx.restore();
        ctx.globalCompositeOperation = "lighter";
      });

      // A reflective floor carries the stage rings beneath the map.
      const floor = ctx.createLinearGradient(0, this.floorY, 0, H);
      floor.addColorStop(0, "rgba(32,9,46,.16)");
      floor.addColorStop(1, "rgba(5,1,9,.76)");
      ctx.fillStyle = floor;
      ctx.fillRect(0, this.floorY, W, H - this.floorY);
      this.floorRings.forEach((ring, index) => {
        const rotation = t * (ring.violet ? -.42 : .42) + ring.phase;
        ctx.beginPath();
        ctx.ellipse(map.x, this.floorY + H * .11, ring.rx, ring.ry, 0, rotation, rotation + 1.05);
        ctx.strokeStyle = ring.violet ? "rgba(203,109,255,.46)" : "rgba(255,197,81,.46)";
        ctx.lineWidth = 1.2;
        ctx.stroke();
        ctx.beginPath();
        ctx.ellipse(map.x, this.floorY + H * .11, ring.rx, ring.ry, 0, rotation + Math.PI, rotation + Math.PI + .58);
        ctx.stroke();
      });

      this.particles.forEach(particle => {
        particle.y -= particle.speed * delta * (this.reduced ? .28 : 1);
        if (particle.y < -8) { particle.y = H + 8; }
        ctx.globalAlpha = particle.alpha;
        ctx.fillStyle = particle.violet ? "#c779ff" : "#ffd077";
        ctx.fillRect(particle.x, particle.y, particle.size, particle.size);
      });
      ctx.globalAlpha = 1;
      const screen = (this.stage.querySelector("[data-screen-label]")?.getAttribute("data-screen-label") || "").toLowerCase();
      if (screen.includes("past conferences")) {
        // The archive page is deliberately a shade quieter than the live
        // experience, while retaining enough colour to remain part of it.
        ctx.globalCompositeOperation = "source-over";
        ctx.fillStyle = "rgba(8,2,14,.16)";
        ctx.fillRect(0, 0, W, H);
      }
      ctx.globalAlpha = 1;
      ctx.globalCompositeOperation = "source-over";
    }

    frame(now) {
      this.raf = requestAnimationFrame(this.frame);
      const delta = Math.min(.05, (now - this.last) / 1000);
      this.last = now;
      this.time += delta * (this.reduced ? .35 : 1);
      this.ctx.clearRect(0, 0, this.width, this.height);
      this.drawScene(delta);
    }
  }

  function boot() {
    document.querySelectorAll(".rc-stage").forEach((stage) => {
      if (stage.__accelerateBackground) return;
      stage.__accelerateBackground = new HologramBackground(stage);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
