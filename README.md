# jira-report
Script to migrate Jira issues to Github Issues

You need to export the following envs:

    export JIRA_USER=user
    export JIRA_PASSWORD=password
    export JIRA_URL=url
    export GITHUB_USER=user
    export GITHUB_PASSWORD=password
    export GITHUB_REPO=repo

You need to customize the Jira Query Search to select the issues to migrate:

    $search = $jiraApi->issue()->search('project = XXX AND affectedVersion is EMPTY AND fixVersion is EMPTY AND type in (Bug, Improvement, "New Feature", Sub-task) AND (labels not in (Roadmap) OR labels is EMPTY) AND status in ("TO DO") ORDER BY priority ASC', null, false, 500);

And run
    
    php jira-to-github.php

