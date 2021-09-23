<?php
/**
 *
 * @author Roy
 */
interface IModelCreator {
    public function getTabels();

	public function getFields($table);

	public function getPrimaryKey($table);

	public function create($table);
}