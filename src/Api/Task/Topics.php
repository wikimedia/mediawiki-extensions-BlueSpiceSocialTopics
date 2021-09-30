<?php
namespace BlueSpice\Social\Topics\Api\Task;

use BlueSpice\Api\Response\Standard;
use BlueSpice\Social\Topics\Entity\Discussion;
use BlueSpice\Social\Topics\Extension;
use BSApiTasksBase;
use Title;

/**
 * Api base class for simple tasks in BlueSpice
 * @package BlueSpiceSocial
 */
class Topics extends BSApiTasksBase {

	/**
	 * Methods that can be called by task param
	 * @var array
	 */
	protected $aTasks = [
		'createPage'
	];

	/**
	 *
	 * @var array
	 */
	protected $aReadTasks = [
		'createPage'
	];

	/**
	 *
	 * @return array
	 */
	protected function getRequiredTaskPermissions() {
		return [
			'createPage' => [ 'read' ]
		];
	}

	/**
	 *
	 * @param \stdClass $taskData
	 * @param array $params
	 * @return Standard
	 */
	public function task_createPage( $taskData, $params ) {
		$result = $this->makeStandardReturn();
		$this->checkPermissions();
		if ( empty( $taskData->page ) ) {
			$result->message = $this->msg(
				'bs-socialtopics-entity-fatalstatus-save-notalkpage'
			)->plain();
			return $result;
		}
		$title = Title::newFromText( $taskData->page );
		if ( !$title ) {
			$result->message = $this->msg(
				'bs-socialtopics-entity-fatalstatus-save-notalkpage'
			)->plain();
			return $result;
		}

		$status = Extension::createDiscussionPage( $title );
		if ( !$status->isOK() ) {
			$result->message = $status->getMessage();
			return $result;
		}

		$factory = $this->getServices()->getService(
			'BSSocialDiscussionEntityFactory'
		);
		$entity = $factory->newFromDiscussionTitle(
			$title
		);
		if ( !$entity instanceof Discussion ) {
			return true;
		}
		if ( !$entity->exists() ) {
			$status = $entity->save();
		}
		if ( !$status->isOK() ) {
			$result->message = $status->getMessage();
			return $result;
		}
		$entity->invalidateCache();
		$result->success = true;
		return $result;
	}
}
