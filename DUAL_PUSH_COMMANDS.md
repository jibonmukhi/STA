# Dual Repository Push Commands

## Current Configuration
Your STA repository is now configured to sync with both GitHub accounts:

- **Primary**: https://github.com/jibonmukhi/STA
- **Secondary**: https://github.com/deshsoft/STA

## Commands for Future Use

### Push to Both Repositories Simultaneously
```bash
git push origin master
```
This single command will push to both repositories at once.

### Push to Individual Repositories
```bash
# Push only to jibonmukhi account
git push https://github.com/jibonmukhi/STA.git master

# Push only to deshsoft account  
git push secondary master
```

### Check Remote Configuration
```bash
git remote -v
```

### Add New Changes and Push to Both
```bash
git add .
git commit -m "Your commit message"
git push origin master
```

## Notes
- The `origin` remote is configured to push to both repositories
- The `secondary` remote points only to the deshsoft repository
- Both repositories will stay synchronized when using `git push origin master`