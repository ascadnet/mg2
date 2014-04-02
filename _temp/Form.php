<?php
namespace anet\lib;

/**
 * Form builder class.
 *
 * @author      Jon Belelieu
 * @link        http://www.ascadnetworks.com/
 * @license     GNU General Public License v3.0
 * @link        http://www.gnu.org/licenses/gpl.html
 * @date        2013-12-05
 * @version     v1.0
 * @project     ANET Framework
 */

class Form {

    /**
     *
     * @param string $action    Form action URL.
     * @param array $fields     Array of fields on the form.
     * @param string $method    Method for the form. Default = GET.
     * @param string $id        ID of the form.
     * @param string $name      Name of the form.
     *
     * $fields Array Example
     *
         $fields = array(
            'name' => array(
                'type'          => 'text',
                'label'         => 'Name',
                'description'   => '',
                'maxlength'     => '50',
                'width'         => '200',
                'value'         => '',
                'class'         => '',
             ),
            'email' => array(
                'type'          => 'email',
                'label'         => 'E-Mail',
                'description'   => '',
                'maxlength'     => '125',
                'width'         => '200',
                'value'         => '',
                'class'         => '',
            ),
            'agree' => array(
                'type'          => 'checkbox',
                'label'         => 'Name',
                'description'   => '',
                'maxlength'     => '125',
                'width'         => '200',
                'value'         => '',
                'class'         => '',
            ),
            'state' => array(
                'type'          => 'select',
                'label'         => 'State',
                'description'   => '',
                'options'       => array(
                    'Value' => 'Display',
                ),
                'width'         => '200',
                'value'         => '',
                'class'         => '',
            ),
         );
     */
    public function __construct($action, $fields, $method = 'GET', $id = '', $name = '')
	{

	}

	/**
	 *
	 * <form action="" method="" id="">
	 */
	public function openForm() {

	}

	/**
	 *
	 * <form action="" method="" id="">
	 */
	public function closeForm() {

	}

    /**
     *
     * <fieldset><legend></legend></fieldset>
     */
    public function makeSection($title = '', $description = '') {

    }

	/**
	 *
	 * <input type="hidden" name="x" value="y" />
	 */
	public function makeHidden() {

	}

    /**
     *
     * <input type="file" name="" />
     */
    public function makeFile() {

    }

	/**
	 *
	 * <input type="text" name="x" />
	 */
	public function makeText() {

	}

    /**
     *
     * <input type="text" name="x" />
     */
    public function makePassword() {

    }

	/**
	 *
	 * <textarea name="x" rows="" cols=""></textarea>
	 */
	public function makeTextarea() {

	}

	/**
	 *
	 * <select name="x"><option value=""></option></select>
	 */
	public function makeSelect() {

	}

	/**
	 *
	 * <input type="checkbox" name="x" value="1">
	 */
	public function makeCheckbox() {

	}


    /**
     *
     * <input type="radio" name="x" value="y">
     */
    public function makeRadio() {

    }

    /**
     *
     * <input type="radio" name="x" value="y">
     */
    public function makeLikert() {

    }

	/**
	 *
	 * <button type="submit">x</button>
	 */
	public function makeSubmit() {

	}

	/**
	 *
	 * <button type="button">x</button>
	 */
	public function makeButton() {

	}


	// ------------------------------
	// HTML5 Field Types

	/**
	 *
	 * <input type="color" name="x">
	 */
	public function makeColor() {

	}

	/**
	 *
	 * <input type="date" name="x">
	 */
	public function makeDate() {

	}

	/**
	 *
	 * <input type="datetime" name="x">
	 */
	public function makeDatetime() {

	}

	/**
	 *
	 * <input type="email" name="x">
	 */
	public function makeEmail() {

	}

	/**
	 *
	 * <input type="month" name="x">
	 */
	public function makeMonth() {

	}

	/**
	 *
	 * max - specifies the maximum value allowed
	 * min - specifies the minimum value allowed
	 * step - specifies the legal number intervals
	 * value - Specifies the default value
	 *
	 * <input type="number" name="x" min="1" max="5">
	 */
	public function makeNumber() {

	}

	/**
	 *
	 * max - specifies the maximum value allowed
	 * min - specifies the minimum value allowed
	 * step - specifies the legal number intervals
	 * value - Specifies the default value
	 *
	 * <input type="range" name="x" min="1" max="10">
	 */
	public function makeRange($name, $value ='') {

	}

	/**
	 *
	 *
	 */
	public function makeSearch() {

	}

	/**
	 *
	 * <input type="tel" name="x">
	 */
	public function makeTel() {

	}

	/**
	 *
	 * <input type="time" name="x">
	 */
	public function makeTime() {

	}

	/**
	 *
	 * <input type="url" name="x">
	 */
	public function makeUrl() {

	}

	/**
	 *
	 * <input type="week" name="x">
	 */
	public function makeWeek() {

	}

}