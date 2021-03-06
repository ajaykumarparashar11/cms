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
class EavValuesFixture
{

    /**
     * Table name.
     *
     * @var string
     */
    public $table = 'eav_values';
/**
 * Table columns.
 *
 * @var array
 */
    public $fields = [
    '_constraints' =>
    [
    'primary' =>
    [
      'type' => 'primary',
      'columns' =>
      [
        0 => 'id',
      ],
      'length' =>
      [
      ],
    ],
    ],
    '_indexes' =>
    [
    'eav_values_eav_attribute_id_index' =>
    [
      'type' => 'index',
      'columns' =>
      [
        0 => 'eav_attribute_id',
      ],
      'length' =>
      [
      ],
    ],
    'eav_values_entity_id_index' =>
    [
      'type' => 'index',
      'columns' =>
      [
        0 => 'entity_id',
      ],
      'length' =>
      [
      ],
    ],
    ],
    '_options' =>
    [
    'engine' => 'InnoDB',
    'collation' => 'utf8_unicode_ci',
    ],
    'id' =>
    [
    'type' => 'integer',
    'unsigned' => false,
    'null' => false,
    'default' => null,
    'comment' => '',
    'autoIncrement' => true,
    'precision' => null,
    ],
    'eav_attribute_id' =>
    [
    'type' => 'integer',
    'length' => 11,
    'unsigned' => false,
    'null' => false,
    'default' => null,
    'comment' => '',
    'precision' => null,
    'autoIncrement' => null,
    ],
    'entity_id' =>
    [
    'type' => 'string',
    'length' => 50,
    'null' => false,
    'default' => null,
    'collate' => 'utf8_unicode_ci',
    'comment' => 'id of the entity in `table`',
    'precision' => null,
    'fixed' => null,
    ],
    'value_datetime' =>
    [
    'type' => 'datetime',
    'length' => null,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    ],
    'value_binary' =>
    [
    'type' => 'binary',
    'length' => null,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    ],
    'value_time' =>
    [
    'type' => 'time',
    'length' => null,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    ],
    'value_date' =>
    [
    'type' => 'date',
    'length' => null,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    ],
    'value_float' =>
    [
    'type' => 'decimal',
    'length' => 10,
    'precision' => 0,
    'unsigned' => false,
    'null' => true,
    'default' => null,
    'comment' => '',
    ],
    'value_integer' =>
    [
    'type' => 'integer',
    'length' => 11,
    'unsigned' => false,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    'autoIncrement' => null,
    ],
    'value_biginteger' =>
    [
    'type' => 'biginteger',
    'length' => 20,
    'unsigned' => false,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    'autoIncrement' => null,
    ],
    'value_text' =>
    [
    'type' => 'text',
    'length' => null,
    'null' => true,
    'default' => null,
    'collate' => 'utf8_unicode_ci',
    'comment' => '',
    'precision' => null,
    ],
    'value_string' =>
    [
    'type' => 'string',
    'length' => 255,
    'null' => true,
    'default' => null,
    'collate' => 'utf8_unicode_ci',
    'comment' => '',
    'precision' => null,
    'fixed' => null,
    ],
    'value_boolean' =>
    [
    'type' => 'boolean',
    'length' => null,
    'null' => true,
    'default' => null,
    'comment' => '',
    'precision' => null,
    ],
    'value_uuid' =>
    [
    'type' => 'string',
    'length' => 36,
    'null' => true,
    'default' => null,
    'collate' => 'utf8_unicode_ci',
    'comment' => '',
    'precision' => null,
    'fixed' => null,
    ],
    'extra' =>
    [
    'type' => 'binary',
    'length' => null,
    'null' => true,
    'default' => null,
    'comment' => 'serialized additional information',
    'precision' => null,
    ],
    ];

    /**
     * Table records.
     *
     * @var array
     */
    public $records = [
    0 =>
    [
    'id' => 1,
    'eav_attribute_id' => 1,
    'entity_id' => '1',
    'value_datetime' => null,
    'value_binary' => null,
    'value_time' => null,
    'value_date' => null,
    'value_float' => null,
    'value_integer' => null,
    'value_biginteger' => null,
    'value_text' => 'Welcome to QuickAppsCMS. This is an example content.',
    'value_string' => null,
    'value_boolean' => null,
    'value_uuid' => null,
    'extra' => '',
    ],
    1 =>
    [
    'id' => 2,
    'eav_attribute_id' => 2,
    'entity_id' => '1',
    'value_datetime' => null,
    'value_binary' => null,
    'value_time' => null,
    'value_date' => null,
    'value_float' => null,
    'value_integer' => null,
    'value_biginteger' => null,
    'value_text' => '<p><strong>Lorem ipsum</strong> dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem.</p>

<p>Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus.</p>

<p>Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus. Nullam quis ante. Etiam sit amet orci eget eros faucibus tincidunt. Duis leo. Sed fringilla mauris sit amet nibh. Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc</p>
',
    'value_string' => null,
    'value_boolean' => null,
    'value_uuid' => null,
    'extra' => '',
    ],
    2 =>
    [
    'id' => 3,
    'eav_attribute_id' => 4,
    'entity_id' => '2',
    'value_datetime' => null,
    'value_binary' => null,
    'value_time' => null,
    'value_date' => null,
    'value_float' => null,
    'value_integer' => null,
    'value_biginteger' => null,
    'value_text' => '<p>Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.&nbsp;<span style="line-height:1.6">Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.</span></p>

<p>Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum.</p>

<p>Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>
',
    'value_string' => null,
    'value_boolean' => null,
    'value_uuid' => null,
    'extra' => 'a:0:{}',
    ],
    3 =>
    [
    'id' => 4,
    'eav_attribute_id' => 3,
    'entity_id' => '1',
    'value_datetime' => null,
    'value_binary' => null,
    'value_time' => null,
    'value_date' => null,
    'value_float' => null,
    'value_integer' => null,
    'value_biginteger' => null,
    'value_text' => 'PHP QuickAppsCMS',
    'value_string' => null,
    'value_boolean' => null,
    'value_uuid' => null,
    'extra' => 'a:2:{i:0;s:1:"1";i:1;s:1:"5";}',
    ],
    ];
}
