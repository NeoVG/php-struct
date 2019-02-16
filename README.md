# php-struct
A "C struct"-like class with type safe attributes and fluent setter interface.

## Synopsis

~~~~php
/**
* @method DataStruct foo(int $value)
* @method DataStruct bar(string $value)
*
* @property-read int    $foo
* @property-read string $bar
*/
class DataStruct extends StructAbstract {}
 
$data = (new DataStruct())
    ->foo(1)
    ->bar('something');
 
echo $data->foo; // '1'
echo $data->bar; // 'something'
echo $data; // '{"foo":1,"bar":"something"}'
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
