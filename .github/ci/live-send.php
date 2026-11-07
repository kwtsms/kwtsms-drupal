<?php

/**
 * @file
 * CI live send for kwtSMS (run with drush php:script).
 *
 * FILE:      .github/ci/live-send.php
 * CREATED:   2026-11-07 10:05 +03
 * TASK:      drupal-ci-install
 * AUTHOR:    M Alhassoun
 * WHAT:      Logs the kwtsms.gateway service in with the CI account and sends one SMS with sendTestSms(), which
 *            always uses test=1, to KWTSMS_TEST_NUMBER. Prints no phone number and no credentials.
 * WHY:       Proves a fresh install works end to end (1.1.0's first test SMS failed with ERR002).
 * USAGE:     vendor/bin/drush php:script "$GITHUB_WORKSPACE/.github/ci/live-send.php"
 * DEPENDS:   Enabled kwtsms module; env KWTSMS_USERNAME, KWTSMS_PASSWORD, KWTSMS_TEST_NUMBER.
 */

$gateway = \Drupal::service('kwtsms.gateway');
$login = $gateway->login((string) getenv('KWTSMS_USERNAME'), (string) getenv('KWTSMS_PASSWORD'));
echo 'login: ' . json_encode(['success' => $login['success'] ?? NULL]) . "\n";
if (empty($login['success'])) {
  exit(1);
}
$result = $gateway->sendTestSms((string) getenv('KWTSMS_TEST_NUMBER'), 'QA-drupal-ci ' . gmdate('YmdHi'));
$api = $result['api_response'] ?? [];
echo 'result: ' . json_encode([
  'success' => $result['success'] ?? NULL,
  'api' => array_intersect_key($api, array_flip(['result', 'code', 'description', 'numbers', 'points-charged'])),
]) . "\n";
exit(!empty($result['success']) ? 0 : 1);
