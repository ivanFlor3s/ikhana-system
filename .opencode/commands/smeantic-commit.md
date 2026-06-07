---
description: Analyzes your Git working tree changes, groups files into logical chunks, and builds semantic/conventional commit messages.
---

You are an expert Git assistant. Your task is to look at the current uncommitted changes in the repository, group them into atomic structural changes, and write semantic commit messages following the Conventional Commits specification.

Below is the current `git status` and a raw `git diff` of the working tree.

### 📋 Git Working Tree Status
!`git status --porcelain`

### 🔍 Git Code Differences
!`git diff`

---

### Your Instructions:

1. **Analyze and Group:** Look at the modified, added, or deleted files. If there are multiple unrelated changes (e.g., a backend API change and a UI component fix), group them into separate, atomic semantic commits.
2. **Format the Messages:** For each logical group, write a commit message adhering to this structure:
   `<type>(<scope>): <short description in lowercase and imperative mood>`
3. **Allowed Semantic Types:**
   - `feat`: A new feature
   - `fix`: A bug fix
   - `docs`: Documentation only changes
   - `style`: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc.)
   - `refactor`: A code change that neither fixes a bug nor adds a feature
   - `test`: Adding missing tests or correcting existing tests
   - `chore`: Changes to the build process or auxiliary tools and libraries such as documentation generation

### Output Format:
Provide your response as a clean, markdown list detailing the file groups and their matching commit messages. If a script is useful to execute these distinct commits sequentially, provide a short bash snippet at the end.

Present your output like this:

### 📦 Proposed Commits Grouping

#### Group 1: [Brief Group Name]
- **Files:** `path/to/file1.ts`, `path/to/file2.ts`
- **Commit Message:** `feat(auth): add JWT expiration handling`

#### Group 2: [Brief Group Name]
- **Files:** `path/to/styles.css`
- **Commit Message:** `style(ui): fix sidebar alignment padding`