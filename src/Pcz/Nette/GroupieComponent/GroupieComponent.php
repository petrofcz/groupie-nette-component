<?php
namespace Pcz\Nette\GroupieComponent;

use Nette\Application\UI\Control;
use Pcz\Groupie\Groupie;

class GroupieComponent extends Control
{
	/** @var callable This function should return result of Pcz\Groupie::buildGroups call. */
	protected $fetchDataCallback;

	/** @var Groupie */
	protected $groupie;

	/** @var string */
	protected $templateFile = __DIR__ . DIRECTORY_SEPARATOR . 'groupieComponent.latte';

	/**
	 * GroupieComponent constructor.
	 * @param callable $fetchDataCallback This function should return result of Pcz\Groupie::buildGroups call.
	 * @param Groupie $groupie
	 */
	public function __construct(callable $fetchDataCallback, Groupie $groupie)
	{
		parent::__construct();
		$this->fetchDataCallback = $fetchDataCallback;
		$this->groupie = $groupie;
	}

	/**
	 * @param string $templateFile
	 * @return $this
	 */
	public function setTemplateFile($templateFile)
	{
		$this->templateFile = $templateFile;
		return $this;
	}

	public function render() {
		$this->template->setFile($this->templateFile);
		$this->fillTemplate();
		$this->template->render();
	}

	protected function fillTemplate() {
		$this->template->groups = call_user_func($this->fetchDataCallback);
		$this->template->columnDefinitions = $columnDefinitions = $this->groupie->getColumnDefinitions();
		$this->template->columnCount = count($columnDefinitions);
		$this->template->levelCount = $this->groupie->getGroupCount();
		$this->template->rowColors = $this->buildRowColors();
	}

	protected function buildRowColors() {
		$groupCount = $this->groupie->getGroupCount();
		$colors = [];
		$colorValue = 255;
		for($i = $groupCount - 1; $i >= 0; $i--) {
			$colors[$i] = '#' . str_repeat(dechex($colorValue), 3);
			$colorValue -= 24;
		}
		return $colors;
	}
}