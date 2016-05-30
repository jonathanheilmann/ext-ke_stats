<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013-2014 Jonathan Heilmann <mail@jonathan-heilmann.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Task 'Update database table' for the 'ke_stats' extension.
 * The function of this code was originally written by Christian Buelter <buelter@kennziffer.com>
 *
 * @author	Jonathan Heilmann <mail@jonathan-heilmann.de>
 * @package	TYPO3
 * @subpackage	tx_kestats
 */
class tx_kestats_updatedb extends \TYPO3\CMS\Scheduler\Task\AbstractTask {
	/**
	 * Task to process cached statdata
	 *
	 * @return boolean
	 */
	public function execute() {
		$extKey = 'ke_stats';
			// include the shared library
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('ke_stats','lib/class.tx_kestats_lib.php'));

			// instantiate the shared library
		$kestatslib = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_kestats_lib');


		if($this->maxExecTime) {
			$maxExecTime = $this->maxExecTime;
		} else {
			$maxExecTime = 90000;
		}
		$startTime = \TYPO3\CMS\Core\Utility\GeneralUtility::milliseconds();
		$oldestEntry = false;
		$counter = 0;
		$counter_invalid = 0;

		do {
				// get oldest entry
			$oldestEntry = $kestatslib->getOldestQueueEntry();

				// process it and delete it
			if ($oldestEntry) {
				$dataArray = unserialize($oldestEntry['data']);

					// compatibility with older versions
				$dataArray['counter'] = $dataArray['counter'] ? $dataArray['counter'] : 1;

				$kestatslib->statData = unserialize($oldestEntry['generaldata']);

					// make sure we only process valid data
				if ($dataArray['category'] && $dataArray['stat_type']) {
					$kestatslib->updateStatisticsTable(
							$dataArray['category'],
							$dataArray['compareFieldList'],
							$dataArray['element_title'],
							$dataArray['element_uid'],
							$dataArray['element_pid'],
							$dataArray['element_language'],
							$dataArray['element_type'],
							$dataArray['stat_type'],
							$dataArray['parent_uid'],
							$dataArray['additionalData'],
							$dataArray['counter']
							);
					$counter++;
				} else {
					$counter_invalid++;
				}

				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_kestats_queue', 'uid=' . $oldestEntry['uid']);
			}

			$runningTime = \TYPO3\CMS\Core\Utility\GeneralUtility::milliseconds() - $startTime;

		} while ($oldestEntry && ($runningTime < $maxExecTime));
/*$output =  'Processed ' . $counter . ' entries in ' . ($runningTime / 1000) . ' seconds.' . ' ';
$output .=  'Ignored ' . $counter_invalid . ' invalid entries.';
t3lib_utility_Debug::debug($output);*/

			//truncate table to reclaim ununsed space in database table if all rows has been processed (table is empty)
		if($GLOBALS['TYPO3_DB']->exec_SELECTcountRows('uid', 'tx_kestats_queue') == 0) {
			$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_kestats_queue');
		} else {
			$GLOBALS['BE_USER']->simplelog('Could not truncate database table \'tx_kestats_queue\'. Maybe you need to run the scheduler task more often.', $extKey, 2);
			return false;
		}

		return true;
	}

	/**
	 * Additional information shown in scheduler tasklist
	 *
	 * @return string
	 */
	public function getAdditionalInformation() {
        return '';
   }
}
?>