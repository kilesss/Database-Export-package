<?php

namespace databaseExporter;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class databaseExporterController
{
    private $filesDirectory = 'storage'.DIRECTORY_SEPARATOR.'sqls'.DIRECTORY_SEPARATOR;
    private $migration_name;
    private $tablesNames = [];
    private $allTables = false;
    public function __construct($migration_name) {
        $this->migration_name = $migration_name;
    }

    public function export() {
        $this->getTableNames();
        $this->exportTables();
    }

    private function getTableNames(){
        $tables = DB::select('SHOW TABLES');
        $tablesKey = "Tables_in_".Config::get('database.connections.mysql.database');
        foreach($tables as $table)
        {
            $this->tablesNames[$table->$tablesKey] = $table->$tablesKey;
        }
    }

    private function checkTableExist(){
        if(empty($this->migration_name))
            exit("Need arguments. Please add table names after command or all for all tables \n");

        if($this->migration_name[0] != 'all'){
            foreach ($this->migration_name as $MName) {
                if (!array_key_exists($MName, $this->tablesNames)) {
                    echo "Table don`t exist  \n";
                    exit();
                }
            }
        }elseif(count($this->migration_name) ==1 && $this->migration_name[0] == 'all'){
            $this->allTables = true;
        }
    }

    private function exportTables(){
        $this->checkTableExist();

        if($this->allTables == true){
            $this->loopAllDatabases();
        }else{
            $this->getTableData($this->migration_name);
        }
    }

    private function getTableData($tableName){
        foreach ($tableName as $Tname) {
            $tableNameFile = $Tname . date('Y-m-d-H-i') . ".sql";
            $sql = 'INSERT INTO `' . $Tname . '` (';
            $fields = Schema::getColumnListing($Tname);
            foreach ($fields as $key => $field) {
                $sql .= '`' . $field . '`';
                $sql = $this->addLastCharacterQuery($fields, $key, $sql);
            }
            $sql .= " VALUES ";
            $data = DB::table($Tname)->get()->toArray();

            if(count($data) == 0){
                echo "Table $Tname is empty!!!  \n";
                continue;
            }
            if(!File::exists($this->filesDirectory)) {
                File::makeDirectory($this->filesDirectory);
            }

            File::put($this->filesDirectory.$tableNameFile, $sql);

            $counter = 0;
            for ($i = 0; $i < count($data); $i++) {
                echo $tableNameFile . "--ROW:--  " . $i . "  ----- \n";
                $counter++;
                $row = "(";
                foreach ($fields as $key => $field) {
                    if ($data[$i]->$field === null) {
                        $data[$i]->$field = "NULL";
                    }
                    $row .= "'" . $data[$i]->$field . "'";
                    $row = $this->addLastCharacterQuery($fields, $key, $row);
                }
                if ($counter == 100) {
                    $row .= ";";
                } else {
                    $row .= ",";
                }
                File::append($tableNameFile, $row);
                unset($row);
                if ($counter == 100) {
                    $counter = 0;
                    File::append($tableNameFile, $sql);
                }
            }
        }
    }

    private function addLastCharacterQuery($array, $key,$row){
        if ($key != array_key_last($array)) {
            $row .= ',';
        } else {
            $row .= ")";
        }
        return $row;
    }

    private function loopAllDatabases(){
            $this->getTableData($this->tablesNames);
    }
}
