<?php
/*****************************************************************
 *	file path and name.php
 *	------------------------
 *  Created			: September 13, 2006
 *  Created by:		: Jason Torgrimson
 *  Copyright		: (c) 2006 Twin Falls High School.
 *	Description		: This file include the classes and methods
 					  to draw all public and administrative header,
					  navigation and footer structures
 ****************************************************************/


/************************************************
 *	Initialize database_driver calss
************************************************/
 
 	$db = new database_driver($config['databaseHost'], $config['databaseUser'], $config['databasePassword'], $config['databaseName']);


/************************************************
 *	Begin database_driver class
************************************************/

	class database_driver	{
	
		var $db = array();
		var $query = array();
		var $querytime = 0;
		var $number_of_queries = 0;
		var $mquery, $session;
		var $error = false;
		var $debug = false;
		
		// Called when the class is initiated and sets all the required variables
		function database_driver($host, $user, $password, $database = NULL)	{
			unset($this->db);
			$this->db['host'] = $host;
			$this->db['user'] = $user;
			$this->db['pass'] = $password;
			$this->db['database'] = $database;
			$this->connect();
		}
		
		function connect($persistant = false)	{
			if($persistant == false)	{
				$this->session = mysql_connect($this->db['host'], $this->db['user'], $this->db['pass']);
			} else	{
				$this->session = mysql_pconnect($this->db['host'], $this->db['user'], $this->db['pass']);
			}
			if(!$this->session) {
				die("Unable to connect to the database server at this time.<br><br>" . mysql_error());
			}
			if(isset($this->db['database']))	{
				mysql_select_db($this->db['database'], $this->session) or die(mysql_error());
			}
		}
		
	/*Code example for QUERY function
		$db->query("SELECT * FROM `table`", 0);
		$db->query("SELECT * FROM `table`", 1);
		while($row = $db->fetch_array('0')	{
			statments;		
		}*/
			
		function query($sql, $id = 0)	{
			
			$this->query[$id] = mysql_query($sql, $this->session);

			if($this->debug) {
				echo '['.$id.'] : ' . $sql . '<br>';
			}
	
			if(mysql_error())	{
				$this->error = true;
				return false;
			} else	{
				return $this->query[$id];
			}
		}
		
		function result($id, $row, $field){
			return mysql_result($this->query[$id], $row, $field);
		}
		
		function num_rows($id=0){
			$num_rows = mysql_num_rows($this->query[$id]);
			return $num_rows;
		}
		
		//Returns mysql_fetch_array
		function fetch_array($id = 0)	{
			return mysql_fetch_array($this->query[$id]);
		}
		
		//Returns associative array as table => field
		function fetch_array_by_table($id = 0)	{
			$results = $this->query[$id];
			$map = array();
			$i = $j = 0;
			$num_fields = mysql_num_fields($results);
		  
			while($i < $num_fields)	{
				$column = mysql_fetch_field($results, $i);
			 
				if(!empty($column->table))	{
					$map[$j++] = array($column->table, $column->name);
				} else	{
					$map[$j++] = array(0, $column->name);
				}
				$i++;
			}
			
			if($row = mysql_fetch_row($this->query[$id]))	{
				$resultRow = array();
				$i = 0;
				
				foreach($row as $index => $field)	{
					list($table, $column) = $map[$index];
					$resultRow[$table][$column] = $row[$index];
					$i++;
				}
				return $resultRow;
			} else	{
				return false;
			}
		}
		
		function multi_query()	{
			$this->query = mysql_query("START TRANSACTION", $this->session);
	
			$rs = func_get_args();
	
			foreach($rs as $index => $args)	{
				$this->mquery = mysql_query(mysql_real_escape_string($args, $this->session));
				if($this->debug) {
					echo "[" . $index . "] :: " . $args;
				}
			}
	
		}
		function begin_transaction()	{
			$this->query = mysql_query("START TRANSACTION", $this->session);
			$this->error = false;
		}
		
		function end_transaction()	{
			if($this->error == true)	{
				mysql_query("ROLLBACK");
				trigger_error($this->last_error(), E_USER_NOTICE);
			} else	{
				mysql_query("COMMIT");
			}
			$this->error = false;
		}

		
		
		function last_error()	{
			return (mysql_errno($this->session)) ? mysql_errno($this->session) . ': ' . mysql_error($this->session) : null;
		}
		
		
		function last_affected($id = 0)	{
			return ($this->query[$id]) ? mysql_affected_rows($this->session) : false;
		}
		
		function last_insert_id()	{
			return mysql_insert_id($this->session);
		}
		
		
		function list_tables() {
			$result = mysql_list_tables($db['database'], $this->session);
	
			if(!$result)	{
				trigger_error("Database has no tables.", E_USER_NOTICE);
				exit;
			} else	{
				$tables = array();
				while ($line = mysql_fetch_array($result))	{
					$tables[] = $line[0];
				}
				return $tables;
			}
		}
		
		function list_fields($table)	{
			$fields = false;
			$cols = $this->query("DESC {$table}");
	
			foreach ($cols as $column)	{
				if(isset($column['COLUMNS']) && !isset($column[0]))	{
					$column[0] = $column['COLUMNS'];
				}
				if(isset($column[0]))	{
					$fields[] = array('name' => $column[0]['Field'], 'type' => $column[0]['Type']);
				}
			}
			return $fields;
		}
		
		function disconnect()	{
			mysql_close($this->connection);
		}
		
		
		function destroy()	{
			settype($this, 'null');
		}
	}
	
?>