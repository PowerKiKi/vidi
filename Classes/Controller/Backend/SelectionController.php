<?php
namespace TYPO3\CMS\Vidi\Controller\Backend;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Vidi\Behavior\SavingBehavior;
use TYPO3\CMS\Vidi\Domain\Repository\ContentRepositoryFactory;
use TYPO3\CMS\Vidi\Domain\Model\Content;
use TYPO3\CMS\Vidi\Mvc\JsonView;
use TYPO3\CMS\Vidi\Mvc\JsonResult;
use TYPO3\CMS\Vidi\Persistence\MatcherObjectFactory;
use TYPO3\CMS\Vidi\Persistence\OrderObjectFactory;
use TYPO3\CMS\Vidi\Persistence\PagerObjectFactory;
use TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments;
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * Controller which handles actions related to Selection in Vidi Backend.
 */
class SelectionController extends ActionController {

	/**
	 * @var \TYPO3\CMS\Vidi\Domain\Repository\SelectionRepository
	 * @inject
	 */
	protected $selectionRepository;

	/**
	 * List action for this controller.
	 *
	 * @return void
	 */
//	public function indexAction() {
//		$this->view->assign('columns', TcaService::grid()->getFields());
//	}

	/**
	 * List Row action for this controller. Output a json list of contents
	 *
	 * @param array $columns corresponds to columns to be rendered.
	 * @param array $matches
	 * @validate $columns TYPO3\CMS\Vidi\Domain\Validator\ColumnsValidator
	 * @validate $matches TYPO3\CMS\Vidi\Domain\Validator\MatchesValidator
	 * @return void
	 */
//	public function listAction(array $columns = array(), $matches = array()) {
//
//		// Initialize some objects related to the query.
//		$matcher = MatcherObjectFactory::getInstance()->getMatcher($matches);
//		$order = OrderObjectFactory::getInstance()->getOrder();
//		$pager = PagerObjectFactory::getInstance()->getPager();
//
//		// Fetch objects via the Content Service.
//		$contentService = $this->getContentService()->findBy($matcher, $order, $pager->getLimit(), $pager->getOffset());
//		$pager->setCount($contentService->getNumberOfObjects());
//
//		// Assign values.
//		$this->view->assign('columns', $columns);
//		$this->view->assign('objects', $contentService->getObjects());
//		$this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
//		$this->view->assign('pager', $pager);
//		$this->view->assign('response', $this->response);
//	}

	/**
	 * Retrieve Content objects first according to matching criteria and then "update" them.
	 * Important to notice the field name can contains a path, e.g. metadata.title and therefore must be analysed.
	 *
	 * Possible values for $matches:
	 * -----------------------------
	 *
	 * $matches = array(uid => 1), will be taken as $query->equals
	 * $matches = array(uid => 1,2,3), will be taken as $query->in
	 * $matches = array(field_name1 => bar, field_name2 => bax), will be separated by AND.
	 *
	 * Possible values for $content:
	 * -----------------------------
	 *
	 * $content = array(field_name => bar)
	 * $content = array(field_name => array(value1, value2)) <-- will be CSV converted by "value1,value2"
	 *
	 * @param string $fieldNameAndPath
	 * @param array $content
	 * @param array $matches
	 * @param string $savingBehavior
	 * @param int $language
	 * @return string
	 */
//	public function updateAction($fieldNameAndPath, array $content, array $matches = array(), $savingBehavior = SavingBehavior::REPLACE, $language = 0) {
//
//		// Instantiate the Matcher object according different rules.
//		$matcher = MatcherObjectFactory::getInstance()->getMatcher($matches);
//		$order = OrderObjectFactory::getInstance()->getOrder();
//
//		// Fetch objects via the Content Service.
//		$contentService = $this->getContentService()->findBy($matcher, $order);
//
//		// Get the real field that is going to be updated.
//		$updatedFieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath);
//
//		// Get result object for storing data along the processing.
//		$result = $this->getJsonResult();
//		$result->setNumberOfObjects($contentService->getNumberOfObjects());
//
//		foreach ($contentService->getObjects() as $index => $object) {
//
//			$identifier = $this->getContentObjectResolver()->getValue($object, $fieldNameAndPath, 'uid', $language);
//
//			// It could be the identifier is not found because the translation
//			// of the record does not yet exist when mass-editing
//			if ((int)$identifier <= 0) {
//				continue;
//			}
//
//			$dataType = $this->getContentObjectResolver()->getDataType($object, $fieldNameAndPath);
//
//			$signalResult = $this->emitProcessContentDataSignal($object, $fieldNameAndPath, $content, $index + 1, $savingBehavior, $language);
//			$contentData = $signalResult->getContentData();
//
//			// Add identifier to content data, required by TCEMain.
//			$contentData['uid'] = $identifier;
//
//			/** @var Content $dataObject */
//			$dataObject = GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Domain\Model\Content', $dataType, $contentData);
//
//			// Properly update object.
//			ContentRepositoryFactory::getInstance($dataType)->update($dataObject);
//
//			// Get the possible error messages and store them.
//			$errorMessage = ContentRepositoryFactory::getInstance()->getErrorMessages();
//			$result->addErrorMessages($errorMessage);
//
//			// We only want to see the detail result if there is one object updated.
//			// Required for inline editing + it will display some useful info on the GUI in the flash messages.
//			if ($contentService->getNumberOfObjects() === 1) {
//
//				// Fetch the updated object from repository.
//				$updatedObject = ContentRepositoryFactory::getInstance()->findByUid($object->getUid());
//
//				// Re-fetch the updated result.
//				$updatedResult = $this->getContentObjectResolver()->getValue($updatedObject, $fieldNameAndPath, $updatedFieldName, $language);
//				if (is_array($updatedResult)) {
//					$_updatedResult = array(); // reset result set.
//
//					/** @var Content $contentObject */
//					foreach ($updatedResult as $contentObject) {
//						$labelField = TcaService::table($contentObject)->getLabelField();
//						$values = array(
//							'uid' => $contentObject->getUid(),
//							'name' => $contentObject[$labelField],
//						);
//						$_updatedResult[] = $values;
//					}
//
//					$updatedResult = $_updatedResult;
//				}
//
//				$labelField = TcaService::table($object)->getLabelField();
//				$processedObjectData = array(
//					'uid' => $object->getUid(),
//					'name' => $object[$labelField],
//					'updatedField' => $fieldNameAndPath,
//					'updatedValue' => $updatedResult,
//				);
//				$result->setProcessedObject($processedObjectData);
//
//			}
//		}
//
//		// Set the result and render the JSON view.
//		$this->getJsonView()->setResult($result);
//		return $this->getJsonView()->render();
//	}

	/**
	 * Returns an editing form for a given data type.
	 *
	 * @param string $dataType
	 * @param array $currentSelection
	 */
	public function editAction($dataType, $currentSelection = array()) {

		$selections = $this->selectionRepository->findByDataTypeForCurrentBackendUser($dataType);
		$this->view->assign('selections', $selections);

//		// Instantiate the Matcher object according different rules.
//		$matcher = MatcherObjectFactory::getInstance()->getMatcher($matches);
//
//		// Fetch objects via the Content Service.
//		$contentService = $this->getContentService()->findBy($matcher);
//
//		$dataType = $this->getFieldPathResolver()->getDataType($fieldNameAndPath);
//		$fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath);
//
//		$fieldType = TcaService::table($dataType)->field($fieldName)->getType();
//		$this->view->assign('fieldType', ucfirst($fieldType));
//		$this->view->assign('dataType', $dataType);
//		$this->view->assign('fieldName', $fieldName);
//		$this->view->assign('matches', $matches);
//		$this->view->assign('fieldNameAndPath', $fieldNameAndPath);
//		$this->view->assign('numberOfObjects', $contentService->getNumberOfObjects());
//		$this->view->assign('editWholeSelection', empty($matches['uid'])); // necessary??
//
//		// Fetch content and its relations.
//		if ($fieldType === TcaService::MULTISELECT) {
//
//			$object = ContentRepositoryFactory::getInstance()->findOneBy($matcher);
//			$identifier = $this->getContentObjectResolver()->getValue($object, $fieldNameAndPath, 'uid');
//			$dataType = $this->getContentObjectResolver()->getDataType($object, $fieldNameAndPath);
//
//			$content = ContentRepositoryFactory::getInstance($dataType)->findByUid($identifier);
//
//			if (!$content) {
//				$message = sprintf('I could not retrieved content object of type "%s" with identifier %s.', $dataType, $identifier);
//				throw new \Exception($message, 1402350182);
//			}
//
//			$relatedDataType = TcaService::table($dataType)->field($fieldName)->getForeignTable();
//
//			// Initialize the matcher object.
//			$matcher = MatcherObjectFactory::getInstance()->getMatcher(array(), $relatedDataType);
//
//			// Default ordering for related data type.
//			$defaultOrderings = TcaService::table($relatedDataType)->getDefaultOrderings();
//			/** @var \TYPO3\CMS\Vidi\Persistence\Order $order */
//			$defaultOrder = GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Persistence\Order', $defaultOrderings);
//
//			// Fetch related contents
//			$relatedContents = ContentRepositoryFactory::getInstance($relatedDataType)->findBy($matcher, $defaultOrder);
//
//			$this->view->assign('content', $content);
//			$this->view->assign('relatedContents', $relatedContents);
//			$this->view->assign('relatedDataType', $relatedDataType);
//			$this->view->assign('relatedContentTitle', TcaService::table($relatedDataType)->getTitle());
//		}
	}

	/**
	 * Get the Vidi Module Loader.
	 *
	 * @return \TYPO3\CMS\Vidi\Service\ContentService
	 */
	protected function getContentService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Service\ContentService');
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\ContentObjectResolver
	 */
	protected function getContentObjectResolver() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\ContentObjectResolver');
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Resolver\FieldPathResolver
	 */
	protected function getFieldPathResolver() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Resolver\FieldPathResolver');
	}

	/**
	 * Return a special view for handling JSON
	 * Goal is to have this view injected but require more configuration.
	 *
	 * @return JsonView
	 */
	protected function getJsonView() {
		if (!$this->view instanceof JsonView) {
			/** @var JsonView $view */
			$this->view = $this->objectManager->get('TYPO3\CMS\Vidi\Mvc\JsonView');
			$this->view->setResponse($this->response);
		}
		return $this->view;
	}

	/**
	 * @return JsonResult
	 */
	protected function getJsonResult() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Mvc\JsonResult');
	}

	/**
	 * Signal that is called for post-processing content data send to the server for update.
	 *
	 * @param Content $contentObject
	 * @param $fieldNameAndPath
	 * @param $contentData
	 * @param $counter
	 * @param $savingBehavior
	 * @param $language
	 * @return ProcessContentDataSignalArguments
	 * @signal
	 */
	protected function emitProcessContentDataSignal(Content $contentObject, $fieldNameAndPath, $contentData, $counter, $savingBehavior, $language) {

		/** @var \TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments $signalArguments */
		$signalArguments = GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Signal\ProcessContentDataSignalArguments');
		$signalArguments->setContentObject($contentObject)
			->setFieldNameAndPath($fieldNameAndPath)
			->setContentData($contentData)
			->setCounter($counter)
			->setSavingBehavior($savingBehavior)
			->setLanguage($language);

		$signalResult = $this->getSignalSlotDispatcher()->dispatch('TYPO3\CMS\Vidi\Controller\Backend\ContentController', 'processContentData', array($signalArguments));
		return $signalResult[0];
	}

	/**
	 * Get the SignalSlot dispatcher.
	 *
	 * @return \TYPO3\CMS\Extbase\SignalSlot\Dispatcher
	 */
	protected function getSignalSlotDispatcher() {
		return $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
	}

	/**
	 * @return \TYPO3\CMS\Vidi\Language\LanguageService
	 */
	protected function getLanguageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Vidi\Language\LanguageService');
	}
}
