# Laravel Cloud Video Call Smoke Test

Use this after your first Laravel Cloud deployment to quickly confirm that the video call flow works in production.

## What You Need

- 1 student account
- 1 instructor account
- 2 devices or 2 separate browsers
- Working camera and microphone on both sides
- A real `https://` Laravel Cloud app URL

## Before Testing

1. Confirm the app is already deployed.
2. Confirm `APP_URL` is your real production URL.
3. Confirm database migrations finished successfully.
4. Confirm both users can log in.
5. Confirm the browser is allowed to use camera and microphone.

## Test 1: Basic Login

1. Open the app as student.
2. Open the app as instructor on another device or browser.
3. Log in on both accounts.

Pass:

- both dashboards load
- no 500 error
- no redirect loop

Fail signs:

- login fails
- dashboard is blank
- assets do not load

## Test 2: Consultation Request Flow

1. On the student side, create a video consultation request.
2. On the instructor side, refresh if needed and check if the request appears.
3. Approve the request.

Pass:

- the request is saved
- the instructor can see it
- approval updates the status

Fail signs:

- request is not created
- approval button fails
- status does not update

## Test 3: Call Start

1. On the instructor side, start the approved video consultation.
2. On the student side, wait for the incoming session prompt or call UI.
3. Join the call from the student side.

Pass:

- camera permission prompt appears
- microphone permission prompt appears
- local preview appears
- call modal opens on both sides

Fail signs:

- no permission prompt
- join button does nothing
- call UI opens but no local video

## Test 4: Signaling

1. Let both users stay on the call screen for at least 15 to 30 seconds.
2. Check if the remote video or connection status appears.

Pass:

- the call connects
- remote audio/video appears
- the call timer starts or session becomes active

Fail signs:

- both users only see themselves
- call stays on connecting forever
- the connection drops immediately

This usually points to one of these:

- signaling issue
- TURN/STUN issue
- blocked network

## Test 5: Cross-Network Test

This is the most important real-world test.

1. Put one device on mobile data.
2. Keep the other device on Wi-Fi.
3. Repeat the call start test.

Pass:

- the call still connects

Fail signs:

- works only on same Wi-Fi
- fails on mobile data or different networks

This usually means the TURN relay is the issue.

## Test 6: End Call

1. End the call from one side.
2. Check if the other side receives the end state.
3. Check if the consultation status becomes `completed`.

Pass:

- call closes cleanly
- status updates to completed
- duration is saved

Fail signs:

- call UI hangs
- status stays `in_progress`
- call ends but session history is wrong

## Test 7: History / Follow-Up Data

1. Open the consultation history after ending the call.
2. Check if the completed session is visible.
3. If your workflow includes notes or summaries, confirm they still work.

Pass:

- completed consultation appears in history
- no missing consultation row

## If The Call Does Not Work

Check these first:

1. `APP_URL` must be `https://...`
2. Camera and mic permissions must be allowed
3. The `webrtc_signals` table must exist
4. Student and instructor must both be participants of the same consultation
5. Test on two different browsers to avoid session conflicts
6. Test on two different networks

## Most Likely Production Risk

If the app deploys correctly but video call fails only on some networks, the most likely issue is the public TURN relay currently used by the app.

Current relay setup is in:

- [student dashboard](/c:/xampp/htdocs/capstone_system2L/resources/views/student/dashboard.blade.php#L6720)
- [instructor dashboard](/c:/xampp/htdocs/capstone_system2L/resources/views/instructor/dashboard.blade.php#L6745)

If needed later, the next upgrade would be moving to your own dedicated TURN service for better production reliability.
