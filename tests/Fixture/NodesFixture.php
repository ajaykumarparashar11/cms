<?php
/**
 * Licensed under The GPL-3.0 License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since    2.0.0
 * @author   Christopher Castro <chris@quickapps.es>
 * @link     http://www.quickappscms.org
 * @license  http://opensource.org/licenses/gpl-3.0.html GPL-3.0 License
 */
namespace QuickApps\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NodesFixture class
 */
class NodesFixture extends TestFixture {

	public $fields = [];
	public $records = [];

	public function init() {
		include_once QA_CORE .'/config/Schema/nodes.php';
		$fixture = new \nodes();
		$this->fields = $fixture->fields;
		$this->records = $fixture->records;
		parent::init();
	}

}