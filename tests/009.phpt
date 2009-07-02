--TEST--
Check for reference serialisation
--INI--
report_memleaks=0
--SKIPIF--
<?php
if(!extension_loaded('igbinary')) {
	echo "skip no igbinary";
}
--FILE--
<?php 

function test($type, $variable, $test) {
	$serialized = igbinary_serialize($variable);
	$unserialized = igbinary_unserialize($serialized);

	echo $type, "\n";
	echo substr(bin2hex($serialized), 8), "\n";
	echo $test || $unserialized == $variable ? 'OK' : 'ERROR';
	echo "\n";
}

$a = array('foo');

test('array($a, $a)', array($a, $a), false);
test('array(&$a, &$a)', array(&$a, &$a), false);

$a = array(null);
$b = array(&$a);
$a[0] = &$b;

test('cyclic', $a, true);

var_dump(unserialize(serialize($a)));
var_dump(igbinary_unserialize(igbinary_serialize($a)));

/*
 * you can add regression tests for your extension here
 *
 * the output of your test code has to be equal to the
 * text in the --EXPECT-- section below for the tests
 * to pass, differences between the output and the
 * expected text are interpreted as failure
 *
 * see php5/README.TESTING for further information on
 * writing regression tests
 */
?>
--EXPECT--
array($a, $a)
14020600140106001103666f6f0601140106000e00
OK
array(&$a, &$a)
14020600140106001103666f6f06010101
OK
cyclic
1401060014010600140106000101
OK
array(1) {
  [0]=>
  &array(1) {
    [0]=>
    array(1) {
      [0]=>
      &array(1) {
        [0]=>
        array(1) {
          [0]=>
          *RECURSION*
        }
      }
    }
  }
}
array(1) {
  [0]=>
  &array(1) {
    [0]=>
    array(1) {
      [0]=>
      &array(1) {
        [0]=>
        array(1) {
          [0]=>
          *RECURSION*
        }
      }
    }
  }
}
