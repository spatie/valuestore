<?php

namespace Spatie\Valuestore\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Valuestore\Valuestore;

class ValuestoreTest extends TestCase
{
    /** @var string */
    protected $storageFile;

    /** @var \Spatie\Valuestore\Valuestore */
    protected $valuestore;

    public function setUp(): void
    {
        parent::setUp();

        $this->storageFile = __DIR__.'/temp/storage.json';

        if (file_exists($this->storageFile)) {
            unlink($this->storageFile);
        }

        $this->valuestore = Valuestore::make($this->storageFile);
    }

    /** @test */
    public function it_can_store_an_array_while_making_the_valuestore()
    {
        $valuestore = Valuestore::make($this->storageFile, ['key' => 'value']);

        $this->assertSame('value', $valuestore->get('key'));
    }

    /** @test */
    public function it_can_store_a_key_value_pair()
    {
        $this->valuestore->put('key', 'value');

        $this->assertSame('value', $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_store_an_array()
    {
        $testArray = ['one' => 1, 'two' => 2];

        $this->valuestore->put('key', $testArray);

        $this->assertSame($testArray, $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_skip_writing_to_disk_if_putting_empty_array()
    {
        $this->valuestore->put('key', 'value');
        touch($this->storageFile, 101);

        $this->valuestore->put([]);

        $this->assertEquals(filemtime($this->storageFile), 101);
    }

    /** @test */
    public function it_can_push_a_value_to_a_non_existing_key()
    {
        $this->valuestore->push('key', 'value');

        $this->assertSame(['value'], $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_push_a_value_to_an_existing_key()
    {
        $this->valuestore->put('key', 'value');

        $this->valuestore->push('key', 'value2');

        $this->assertSame(['value', 'value2'], $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_prepend_a_value_to_a_non_existing_key()
    {
        $this->valuestore->prepend('key', 'value');

        $this->assertSame(['value'], $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_prepend_a_value_to_an_existing_key()
    {
        $this->valuestore->put('key', 'value');

        $this->valuestore->prepend('key', 'value2');

        $this->assertSame(['value2', 'value'], $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_determine_if_the_store_holds_a_value_for_a_given_name()
    {
        $this->assertFalse($this->valuestore->has('key'));

        $this->valuestore->put('key', 'value');

        $this->assertTrue($this->valuestore->has('key'));
    }

    /** @test */
    public function it_will_return_the_default_value_when_using_a_non_existing_key()
    {
        $this->assertSame('default', $this->valuestore->get('key', 'default'));
    }

    /** @test */
    public function it_can_store_an_integer()
    {
        $this->valuestore->put('number', 1);

        $this->assertSame(1, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_provides_a_chainable_put_method()
    {
        $this->valuestore
            ->put('number', 1)
            ->put('string', 'hello');

        $this->assertSame(1, $this->valuestore->get('number'));
        $this->assertSame('hello', $this->valuestore->get('string'));
    }

    /** @test */
    public function it_will_return_null_for_a_non_existing_value()
    {
        $this->assertNull($this->valuestore->get('non existing key'));
    }

    /** @test */
    public function it_can_overwrite_a_value()
    {
        $this->valuestore->put('key', 'value');

        $this->valuestore->put('key', 'otherValue');

        $this->assertSame('otherValue', $this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_fetch_all_values_at_once()
    {
        $this->valuestore->put('key', 'value');

        $this->valuestore->put('otherKey', 'otherValue');

        $this->assertSame([
            'key' => 'value',
            'otherKey' => 'otherValue',
        ], $this->valuestore->all());
    }

    /** @test */
    public function it_can_store_multiple_value_pairs_in_one_go()
    {
        $values = [
            'key' => 'value',
            'otherKey' => 'otherValue',
        ];

        $this->valuestore->put($values);

        $this->assertSame('value', $this->valuestore->get('key'));

        $this->assertSame($values, $this->valuestore->all());
    }

    /** @test */
    public function it_can_store_values_without_forgetting_the_old_values()
    {
        $this->valuestore->put('test1', 'value1');

        $this->valuestore->put('test2', 'value2');

        $this->assertSame([
            'test1' => 'value1',
            'test2' => 'value2',
        ], $this->valuestore->all());

        $this->valuestore->put(['test3' => 'value3']);

        $this->assertSame([
            'test1' => 'value1',
            'test2' => 'value2',
            'test3' => 'value3',
        ], $this->valuestore->all());
    }

    /** @test */
    public function it_can_fetch_all_values_starting_with_a_certain_value()
    {
        $this->valuestore->put([
            'group1Key1' => 'valueGroup1Key1',
            'group1Key2' => 'valueGroup1Key2',
            'testgroup1' => 'valueTestGroup1',
            'group2Key1' => 'valueGroup2Key1',
            'group2Key2' => 'valueGroup2Key2',
        ]);

        $expectedArray = [
            'group1Key1' => 'valueGroup1Key1',
            'group1Key2' => 'valueGroup1Key2',
        ];

        $this->assertSame($expectedArray, $this->valuestore->allStartingWith('group1'));
    }

    /** @test */
    public function it_can_fetch_all_values_starting_with_a_default_value()
    {
        $this->valuestore->put([
            'group1Key1' => 'valueGroup1Key1',
            'group1Key2' => 'valueGroup1Key2',
            'testgroup1' => 'valueTestGroup1',
            'group2Key1' => 'valueGroup2Key1',
            'group2Key2' => 'valueGroup2Key2',
        ]);

        $expectedArray = [
            'group1Key1' => 'valueGroup1Key1',
            'group1Key2' => 'valueGroup1Key2',
            'testgroup1' => 'valueTestGroup1',
            'group2Key1' => 'valueGroup2Key1',
            'group2Key2' => 'valueGroup2Key2',
        ];

        $this->assertSame($expectedArray, $this->valuestore->allStartingWith());
    }

    /** @test */
    public function it_can_forget_a_value()
    {
        $this->valuestore->put('key', 'value');

        $this->valuestore->put('otherKey', 'otherValue');

        $this->valuestore->put('otherKey2', 'otherValue2');

        $this->valuestore->forget('otherKey');

        $this->assertSame('value', $this->valuestore->get('key'));

        $this->assertNull($this->valuestore->get('otherKey'));

        $this->assertSame('otherValue2', $this->valuestore->get('otherKey2'));
    }

    /** @test */
    public function it_can_flush_the_entire_value_store()
    {
        $this->valuestore->put('key', 'value');

        $this->valuestore->put('otherKey', 'otherValue');

        $this->valuestore->flush();

        $this->assertNull($this->valuestore->get('key'));

        $this->assertNull($this->valuestore->get('otherKey'));
    }

    /** @test */
    public function it_can_flush_all_keys_starting_with_a_certain_string()
    {
        $this->valuestore->put([
            'group1' => 'valueGroup1',
            'group1Key1' => 'valueGroup1Key1',
            'group1Key2' => 'valueGroup1Key2',
            'group2Key1' => 'valueGroup2Key1',
            'group2Key2' => 'valueGroup2Key2',
        ]);

        $this->valuestore->flushStartingWith('group1');

        $expectedArray = [
            'group2Key1' => 'valueGroup2Key1',
            'group2Key2' => 'valueGroup2Key2',
        ];

        $this->assertSame($expectedArray, $this->valuestore->all());
    }

    /** @test */
    public function it_will_return_an_empty_array_when_getting_all_content()
    {
        $this->assertSame([], $this->valuestore->all());
    }

    /** @test */
    public function it_can_get_and_forget_a_value()
    {
        $this->valuestore->put('key', 'value');

        $this->assertSame('value', $this->valuestore->pull('key'));

        $this->assertNull($this->valuestore->get('key'));
    }

    /** @test */
    public function it_can_increment_a_new_value()
    {
        $returnValue = $this->valuestore->increment('number');

        $this->assertSame(1, $returnValue);

        $this->assertSame(1, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_can_increment_an_existing_value()
    {
        $this->valuestore->put('number', 1);

        $returnValue = $this->valuestore->increment('number');

        $this->assertSame(2, $returnValue);

        $this->assertSame(2, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_can_increment_a_value_by_another_value()
    {
        $returnValue = $this->valuestore->increment('number', 2);

        $this->assertSame(2, $returnValue);

        $this->assertSame(2, $this->valuestore->get('number'));

        $returnValue = $this->valuestore->increment('number', 2);

        $this->assertSame(4, $returnValue);

        $this->assertSame(4, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_can_decrement_a_new_value()
    {
        $returnValue = $this->valuestore->decrement('number');

        $this->assertSame(-1, $returnValue);

        $this->assertSame(-1, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_can_decrement_an_existing_value()
    {
        $this->valuestore->put('number', 10);

        $returnValue = $this->valuestore->decrement('number');

        $this->assertSame(9, $returnValue);

        $this->assertSame(9, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_can_decrement_a_value_by_another_value()
    {
        $returnValue = $this->valuestore->decrement('number', 2);

        $this->assertSame(-2, $returnValue);

        $this->assertSame(-2, $this->valuestore->get('number'));

        $returnValue = $this->valuestore->decrement('number', 2);

        $this->assertSame(-4, $returnValue);

        $this->assertSame(-4, $this->valuestore->get('number'));
    }

    /** @test */
    public function it_implements_array_access()
    {
        $this->assertEmpty($this->valuestore['key']);

        $this->valuestore['key'] = 'value';

        $this->assertNotEmpty($this->valuestore['key']);

        $this->assertSame('value', $this->valuestore['key']);

        unset($this->valuestore['key']);

        $this->assertEmpty($this->valuestore['key']);

        $this->assertNull($this->valuestore['key']);

        $this->assertFalse(isset($this->valuestore['key']));

        $this->valuestore['key'] = 'value';

        $this->assertTrue(isset($this->valuestore['key']));
    }

    /** @test */
    public function it_implements_countable()
    {
        $this->assertCount(0, $this->valuestore);

        $this->valuestore->put('key', 'value');

        $this->assertCount(1, $this->valuestore);
    }

    /** @test */
    public function it_will_delete_the_underlying_file_when_no_values_are_left_in_the_store()
    {
        $this->assertFileNotExists($this->storageFile);

        $this->valuestore->put('key', 'value');

        $this->assertFileExists($this->storageFile);

        $this->valuestore->forget('key');

        $this->assertFileNotExists($this->storageFile);

        $this->valuestore->put('key', 'value');

        $this->valuestore->flush();

        $this->assertFileNotExists($this->storageFile);
    }

    /** @test */
    public function the_all_function_will_always_return_an_array()
    {
        $this->assertFileNotExists($this->storageFile);

        touch($this->storageFile);

        $this->assertStringEqualsFile($this->storageFile, '');

        $this->assertIsArray($this->valuestore->all());

        $this->valuestore->flush();

        $this->assertFileNotExists($this->storageFile);
    }
}
