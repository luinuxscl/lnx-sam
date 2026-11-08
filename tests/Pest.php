<?php

/*
|--------------------------------------------------------------------------
| Test setup
|--------------------------------------------------------------------------
|
| Every case starts from an empty runtime: the registry is a singleton in the
| host, so a test that declares a contributor must not leak into the next one.
|
*/

uses()->beforeEach(function (): void {
    resetSamRuntime();
})->in(__DIR__);
