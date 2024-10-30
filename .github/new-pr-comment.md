### 👋 Thanks for opening a pull request!

If you are new, please check out the trimmed down summary of the deployment process below:

1. 👀 Observe the CI jobs and tests to ensure they are passing
1. ✔️ Obtain an approval/review on this pull request
1. 🚀 Deploy your pull request to the **dev** environment with `.deploy to dev`
1. 🚀 Deploy your pull request to the **test** environment with `.deploy to test`
1. 🚀 Deploy your pull request to the **stage** environment with `.deploy to stage` or by merging the PR
1. 🚀 Deploy to the **production** environment by creating a new release from the **main** branch

    > If anything goes wrong, rollback with `.deploy main`

1. 🎉 Merge!

If you have a larger change and want to block deployments, you can run `.lock --reason <reason>` to lock all other deployments (remove with `.unlock`)

> You can view the branch deploy [usage guide](https://github.com/github/branch-deploy/blob/main/docs/usage.md) for additional information