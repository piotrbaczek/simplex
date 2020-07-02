<?php
/** 
 * Simple CSV reading class from PHP5. 
 * 
 * The GNU License. 
 * 
 * Copyright (c) 2012 Jean-Marie Comets 
 * 
 * This program is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version. 
 * 
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 * 
 * @author Jean-Marie Comets <jean.marie.comets@gmail.com> 
 * @version 1.0 
 * 
 * @usage : 
 * $csv = new Csv_Reader("foo.bar"); // instances class 
 * echo $csv->dump(); // returns data (raw HTML) 
 * var_dump($csv->get()); // returns data (2D array) 
 */

/**
 * Class Csv_Reader
 * @deprecated
 */
class Csv_Reader 
{ 
    private $file; 
    private $delimiter; 
    private $data; 
    
    public function __construct($filename, $delimiter = null) // constructor 
    { 
        // load all lines of CSV file 
        
        $this->file = file($filename); 
        
        // if delimiter is not given, find it yourself 
        
        $this->delimiter = ($delimiter === null) ? $this->delim() : $delimiter; 
        
        // parse data with delimiter 
        
        $this->parse(); 
    } 
    
    public function __destruct() // destructor 
    { 
        // not much to do as a matter of a fact =) 
    } 
    
    private function delim() // used to initialize $this->delimiter 
    { 
        // specify allowed field delimiters and order by priority 
        
        $delimiters = array( 
            "comma"     => ",", 
            "semicolon" => ";", 
            "colon"     => ":", 
            "pipe"         => "|", 
            "tab"         => "\\t" 
        ); 
        
        // compare for each delimiter average count of non-empty cells (long) 
        
        $max_count = 0; $result = 0; // initialize 
        
        foreach($delimiters as $index => $delimiter) 
        { 
            $average_count = 0; // initialize the average count 
            
            foreach($this->file as $row) 
            { 
                // split with delimiter 
                
                $tabRow = explode($delimiter,$row); 
                
                // add count of non-empty cells 
                
                $average_count += count(array_filter($tabRow)); 
            } 
            
            // calculate average 
            
            $average_count /= count($this->file); 
            
            // the average count is bigger than the previous 
            
            if($average_count > $max_count) 
            { 
                
                // change max average count 
                
                $max_count = $average_count; 
                
                // set result as current index 
                
                $result = $index; 
            } 
        } 
        
        // worst case scenario : returns the first delimiter which has the most priority 
        
        return $delimiters[$result]; 
    } 
    
    private function parse() // parse data with set delimiter : initialize $this->data 
    { 
        // initialize data 
        
        $this->data = array(); 
        
        foreach($this->file as $row) 
        { 
            // parse row and put into $this->data 
            
            $this->data[] = explode($this->delimiter,$row); 
        } 
    } 
    
    public function get() // get parsed data (string * array) 
    { 
        return $this->data; // way to much to do here 
    } 
    
    public function dump() // dump as raw HTML table 
    { 
        $tbody = "<tbody>"; // start tbody 
        
        foreach($this->data as $row) 
        { 
            
            $tbody .= "<tr>"; //beginning of new row 
            
            foreach($row as $cell) 
            { 
                $tbody .= "<td>".$cell."</td>"; // add cell 
            } 
            
            $tbody .= "</tr>"; // end row 
        } 
        
        $tbody .= "</tbody>"; // end tbody 
        
        return "<table>".$tbody."</table>"; // encapsulate in table tags 
    } 
}
?>
