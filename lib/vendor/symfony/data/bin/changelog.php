<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Outputs formatted Subversion log entries.
 *
 * Usage: php data/bin/changelog.php -r12345:67890 /branches/1.3
 *
 * @package    symfony
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: changelog.php 24071 2010-11-17 07:36:21Z Kris.Wallsmith $
 */
require_once dirname(__FILE__).'/../../lib/task/sfFilesystem.class.php';

if (!isset($argv[1]))
{
  throw new Exception('You must provide a revision range (-r123:456)');
}

if (!isset($argv[2]))
{
  throw new Exception('You must provide a repository path (/branches/1.3)');
}

$filesystem = new sfFilesystem();

list($out, $err) = $filesystem->execute('svn info --xml');
$info = new SimpleXMLElement($out);

list($out, $err) = $filesystem->execute(vsprintf('svn log %s --xml %s', array_map('escapeshellarg', array(
  $argv[1],
  (string) $info->entry->repository->root.$argv[2],
))));
$log = new SimpleXMLElement($out);

foreach ($log->logentry as $logentry)
{
  echo sprintf(' * [%d] %s', $logentry['revision'], trim(preg_replace('/\s*\[[\d\., ]+\]\s*/', '', (string) $logentry->msg)));
  echo PHP_EOL;
}
