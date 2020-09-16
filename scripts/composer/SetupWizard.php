<?php

declare(strict_types = 1);

namespace Subsite\composer;

use Composer\Json\JsonFile;
use Composer\Script\Event;

/**
 * Setup wizard to handle user input during initial composer installation.
 *
 * @phpcs:ignorefile Generic.PHP.ForbiddenFunctions
 */
class SetupWizard {

  /**
   * The setup wizard.
   *
   * @param \Composer\Script\Event $event
   *   The Composer event that triggered the wizard.
   *
   * @return bool
   *   TRUE on success.
   *
   * @throws \Exception
   *   Thrown when an error occurs during the setup.
   */
  public static function setup(Event $event): bool {
    // Ask for the project name, and suggest the various machine names.
    $params = [
      'project_profile' => 'openeuropa',
      'project_id' => 'subsite',
      'project_name' => 'My Website',
      'project_vendor' => 'digit',
      'project_description' => 'Drupal 8 template for websites hosted in DIGIT.'
    ];

    $options = [
      'minimal',
      'standard',
      'oe_profile',
      ];

    $questions = [
      'project_id' => 'What is the project id?',
      'project_vendor' => 'What vendor will be used?',
      'project_description' => 'Provide a description.',
      'project_name' => 'What is the Website name?',
      ];

    // We are providing multiple options for profile.
    $params['project_profile'] = $event->getIO()->select('<info>Select the installation profile?</info> [<comment>' . $params['project_profile'] . '</comment>]? ', $options, $params['project_profile']);
    exec("find ./ -type f  ! -path '*/web/*' ! -path '*/vendor/*' ! -path '*/.git/*' ! -path '*/scripts/*' -exec sed -i 's/token_project_profile/{$options[$params["project_profile"]]}/g' {} +");

    foreach ($questions as $key => $question) {
      $params[$key] = $event->getIO()->ask('<info>' . $question . '</info> [<comment>' . $params[$key] . '</comment>]? ', $params[$key]);
      exec("find ./ -type f  ! -path '*/web/*' ! -path '*/vendor/*' ! -path '*/.git/*' ! -path '*/scripts/*' -exec sed -i 's/token_{$key}/{$params[$key]}/g' {} +");
    }

    // Clean up setup wizard files.
    unlink('scripts/composer/SetupWizard.php');
    rmdir('scripts/composer');
    rmdir('scripts');

    return TRUE;
  }

}
