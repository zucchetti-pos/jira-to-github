<?php

require_once 'vendor/autoload.php';

$jiraUser = getenv('JIRA_USER');
$jiraPassword = getenv('JIRA_PASSWORD');
$jiraUrl = getenv('JIRA_URL');
$jiraApi = new \JiraClient\JiraClient($jiraUrl, $jiraUser, $jiraPassword);

$search = $jiraApi->issue()->search('project = XXX AND affectedVersion is EMPTY AND fixVersion is EMPTY AND type in (Bug, Improvement, "New Feature", Sub-task) AND (labels not in (Roadmap) OR labels is EMPTY) AND status in ("TO DO") ORDER BY priority ASC', null, false, 500);

$githubClient = new \Github\Client(
    new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
);
$githubUser = getenv('GITHUB_USER');
$githubPassword = getenv('GITHUB_PASSWORD');
$githubRepo = getenv('GITHUB_REPO');
$githubClient->authenticate($githubUser, $githubPassword, \Github\Client::AUTH_HTTP_PASSWORD);

echo $search->getTotal(), " tarefas", "\n\n";

foreach ($search->getList() as $key => $issue) {
    $issueData = [];
    $issueData['title'] = $issue->getSummary();
    $issueData['body'] = $issue->getDescription();
    if ($issueData['title'] == '' || $issueData['title'] == null || !isset($issueData['title'])) {
        $issueData['title'] = ' ';
    }
    if ($issueData['body'] == '' || $issueData['body'] == null || !isset($issueData['body'])) {
        $issueData['body'] = ' ';
    }

    $labels = [];
    $labels[] = strtolower($issue->getIssueType()->getName());
    $labels[] = strtolower($issue->getStatus()->getName());
    foreach ($issue->getLabels() as $key => $value) {
        $labels[] = strtolower($value);
    }
    $labels[] = strtolower($issue->getPriority()->getName());
    $issueData['labels'] = $labels;
    $githubIssue = $githubClient->api('issue')->create($githubUser, $githubRepo, $issueData);
}
