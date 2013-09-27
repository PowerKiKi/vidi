<?php
namespace TYPO3\CMS\Vidi\ViewHelpers\Grid\Row;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * View helper for rendering a checkbox.
 */
class CheckboxViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Returns a checkbox for the grids.
	 *
	 * @param \TYPO3\CMS\Vidi\Domain\Model\Content $object
	 * @param  int $offset
	 * @return string
	 */
	public function render(\TYPO3\CMS\Vidi\Domain\Model\Content $object, $offset) {
		return sprintf('<input type="checkbox" class="checkbox-row" data-index="%s" data-uid="%s"/>',
			$offset,
			$object->getUid()
		);
	}
}

?>