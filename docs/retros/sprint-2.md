Things to work on;

  --team communication
        updates rather than just starting/stopping

  --more eyes on PRs
        been doing well, just need to keep at it

## Danny — Student Account (#30)

### What went well
- Finished the student account story: program selection, program map, and prerequisite checks for adding and removing classes
- Students can only see and change their own plan, and other roles get a 403
- 17 Pest tests cover the rules, and they pass in CI
- Wrote `docs/student-account.md` so the team can see how it works and which files it touches
- Kept most of the work in new files, so it didn't collide with the admin and advisor work

### What didn't go well
- The live site had no programs, terms, or prerequisites, so the demo had to run locally with `php artisan serve`
- Local databases got out of sync after switching branches ("table already exists"), and `migrate:fresh --seed` was the fix
- Merge conflicts in the shared layout file (`layouts/app.blade.php`) when several branches edited the header
- Less time this sprint because of a heavy work schedule

### What to work on
- Real (or fun fake) course and program seed data, so demos work on the live site
- Post a heads-up when changing shared files like the layout or migrations
- Keep reviewing PRs and give updates in Slack instead of going quiet
