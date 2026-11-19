# Zoom Interactive Sessions

## Implementation checklist

- [x] ~~Add session fields for the stream provider and Zoom meeting details.~~
- [x] ~~Add Zoom settings placeholders to the environment configuration; do not store secrets in code or the database.~~
- [x] ~~Update the admin session form so an admin can choose Livepush or Zoom and provide the applicable details.~~
- [x] ~~Preserve the existing Livepush flow for one-way sessions.~~
- [x] ~~Add a server-only endpoint that creates a short-lived Zoom Meeting SDK signature for an authorised attendee.~~
- [x] ~~Add a Zoom meeting route that loads the Zoom Meeting SDK inside the portal.~~
- [x] ~~Restrict Zoom joining to logged-in attendees with access to the relevant portal session.~~
- [x] ~~Use the attendee’s portal name as the Zoom display name.~~
- [x] ~~Keep meeting numbers, passcodes, SDK credentials, and signatures out of page source until required for the authorised join flow.~~
- [x] ~~Show a clear unsupported/unconfigured state when Zoom credentials or meeting details are missing.~~
- [x] ~~Add the required Zoom browser permissions and isolated frame setup for camera, microphone, and screen sharing.~~
- [x] ~~Verify PHP syntax, routes, and automated tests.~~
- [x] ~~Document the exact Zoom Marketplace app and environment values still required from the Zoom account owner.~~

## Required Zoom account setup

- [ ] Create a **Meeting SDK** app in the Zoom Marketplace.
- [ ] Add the staging portal domain to the app’s allow list. Add production only when production rollout is approved.
- [ ] Provide the Meeting SDK Client ID and Client Secret through environment variables.
- [ ] Configure each interactive meeting or webinar in Zoom, including its passcode, waiting room, host, and participant settings.

## Staging environment values

Add these values to the ignored `.env` file on the staging server. The empty
placeholders are also in [`.env.example`](.env.example). Never commit the
Client Secret.

```ini
zoom.clientId = YOUR_ZOOM_MEETING_SDK_CLIENT_ID
zoom.clientSecret = YOUR_ZOOM_MEETING_SDK_CLIENT_SECRET
zoom.sdkVersion = YOUR_APPROVED_MEETING_SDK_WEB_VERSION
```

`zoom.sdkVersion` must exactly match the Meeting SDK Web assets used by Zoom.

## Completion log

- 2026-11-16 — Added an unrun migration for Zoom and dedicated Livepush
  session columns. It copies existing temporary Livepush values out of the
  legacy Vimeo column; it has not been executed locally or on staging.
- 2026-11-16 — Added the Livepush/Zoom admin choice, protected signature
  endpoint, isolated Zoom join frame, and clear unconfigured states.
- 2026-11-16 — PHP lint, route discovery, and automated tests passed.
