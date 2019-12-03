# php-struct
A "C struct"-like class with type safe attributes and fluent setter interface.

## Synopsis

~~~~php
/**
 * @property int    $foo
 * @property string $bar
 *
 * @method $this withFoo(int $value)
 * @method $this withBar(string $value)
 */
class DataStruct extends StructAbstract
{
}

$data = DataStruct::createFromJson(
    '{"foo":1,"bar":"something"}'
);

$data = DataStruct::createFromArray([
    'foo' => 1,
    'bar' => 'something',
]);

$data = (new DataStruct())
    ->withFoo(1)
    ->withBar('something');

$data = new DataStruct();
$data->foo = 1;
$data->bar = 'something';

echo $data->foo; // '1'
echo $data->bar; // 'something'
echo (string)$data; // '{"foo":1,"bar":"something"}'
~~~~

## Description

A convenient way to create strictly typed classes for arbitrary data structures without having to write lots of getters and setters.
For even more convenience, setters can be chained.

Additionally, a cast to string of an instantiated object returns JSON!

## How it works

StructAbstract reads the @property-read annotations and creates an internal representation of them, which is then used by the magic methods __call() and __get() to emulate strictly types setters and readable properties.

## Available Data Types for Properties

- bool
- int
- double
- string
- array
- object
- callable

## Storing Objects

When storing objects in Structs, the type in the annotations must be the actual class (or some superclass of it), because arguments passed to the setter will be checked with instanceof.

## Default Values
The default value of each property is null. You can override this by adding a private or protected property of the same name with a default value.

~~~~php
/**
 * @method WithDefaultStruct someproperty(int $value)
 *
 * @property-read string $someproperty
 */
class WithDefaultStruct extends StructAbstract
{
    protected $someproperty = 'default value';
}

$data = new WithDefaultStruct();

echo $data->someproperty; // 'default value'
~~~~

## Array Properties

~~~~php
/**
 * @property string[] $strings
 * @property ChildStruct[] $childs
 */
class ArrayStruct extends StructAbstract
{
}

$array = ArrayStruct::createFromArray([
    'strings' => ['foo', 'bar'],
    'childs' => [
        ['property' => 'value1'],
        ['property' => 'value2'],
    ],
]);

echo $array->strings[0]; // 'foo'
echo $array->childs[0]->property; // 'value1'
~~~~
