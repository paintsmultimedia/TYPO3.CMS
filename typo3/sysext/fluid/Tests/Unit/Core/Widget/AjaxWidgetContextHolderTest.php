<?php
namespace TYPO3\CMS\Fluid\Tests\Unit\Core\Widget;

/*
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

/**
 * Test case
 */
class AjaxWidgetContextHolderTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @test
     * @expectedException \TYPO3\CMS\Fluid\Core\Widget\Exception\WidgetContextNotFoundException
     */
    public function getThrowsExceptionIfWidgetContextIsNotFound()
    {
        /** @var \TYPO3\CMS\Fluid\Core\Widget\AjaxWidgetContextHolder $ajaxWidgetContextHolder */
        $ajaxWidgetContextHolder = $this->getMock(\TYPO3\CMS\Fluid\Core\Widget\AjaxWidgetContextHolder::class, array('dummy'), array(), '', false);
        $ajaxWidgetContextHolder->get(42);
    }
}
