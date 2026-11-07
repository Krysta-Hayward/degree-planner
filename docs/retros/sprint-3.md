## Danny — Admin Define and Edit Programs (#34)

### What went well
- Finished #34: admins can create and edit degree programs with a description, required courses, and requirement groups
- Renaming a program keeps students who chose it on the program, and the update runs in a transaction so nothing is left half-changed
- ProgramPolicy limits creating and editing programs to admins
- 12 Pest tests cover permissions, validation, editing, and renaming, and they pass in CI
- Dark mode toggle and responsive navigation (#66) were merged at the start of the sprint
- Tim's database and seeders and Krysta's admin setup made it easy to build on

### What didn't go well
- Programs are linked to students by name, so renaming caused bugs where data was kept and doubled before it got fixed
- Seeding pulls in every entry, which makes resetting and demoing slower
- I went quiet for the second half of the sprint because of things outside school, so I missed standups and PR reviews

### What to work on
- Link students to programs by ID instead of name so renames can't break plans
- More practical seed data that's smaller and easier to reset
- Post standup updates even when I'm busy, and get back to reviewing PRs
- Agree with Krysta on defining shared view styles so our pages look consistent
