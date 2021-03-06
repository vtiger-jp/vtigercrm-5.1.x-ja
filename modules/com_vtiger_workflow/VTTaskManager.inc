<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

	/**
	 * Functionality to save and retrieve Tasks from the database.
	 */
	class VTTaskManager{
		function __construct($adb){
			$this->adb = $adb;
		}
		
		/**
		 * Save the task into the database.
		 *
		 * When a new task is saved for the first time a field is added to it called
		 * id that stores the task id used in the database.
		 *
		 * @param $summary A summary of the task instance.
		 * @param $task The task instance to save.
		 * @return The id of the task 
		 */
		public function saveTask($task){
			$adb = $this->adb;
			if(is_numeric($task->id)){//How do I check whether a member exists in php?
				$taskId = $task->id;
				$adb->pquery("update com_vtiger_workflowtasks set summary=?, task=? where task_id=?", 
							array($task->summary, serialize($task), $taskId));	
				return $taskId;
			}else{
				$taskId = $adb->getUniqueID("com_vtiger_workflowtasks");
				$task->id = $taskId;
				$adb->pquery("insert into com_vtiger_workflowtasks 
							(task_id, workflow_id, summary, task) 
							values (?, ?, ?, ?)", 
					array($taskId, $task->workflowId, $task->summary, serialize($task)));
				return $taskId;
			}
		}
		
		public function deleteTask($taskId){
			$adb = $this->adb;
			$adb->pquery("delete from com_vtiger_workflowtasks where task_id=?", array($taskId));
		}
		
		
		/**
		 * Create a new class instance
		 */
		public function createTask($taskType, $workflowId){
			$taskClass = $taskType;
			$this->requireTask($taskType);
			$task = new $taskClass();
			$task->workflowId=$workflowId;
			$task->summary = "";
			$task->active=true; 
			return $task;
		}
		
		
		/**
		 * Retrieve a task from the database
		 *
		 * @param $taskId The id of the task to retrieve.
		 * @return The retrieved task.
		 */
		public function retrieveTask($taskId){
			$adb = $this->adb;
			$result = $adb->pquery("select task from com_vtiger_workflowtasks where task_id=?", array($taskId));
			$data = $adb->raw_query_result_rowdata($result, 0);
			$task = $data["task"];
			return $this->unserializeTask($task);
		}
		
		
		/**
		 *
		 */
		public function getTasksForWorkflow($workflowId){
			$adb = $this->adb;
			$result = $adb->pquery("select task from com_vtiger_workflowtasks 
									where workflow_id=?", 
				array($workflowId));
			return $this->getTasksForResult($result);
		}


		/**
		 * 
		 */
		public function unserializeTask($str){
			$this->requireTask(self::taskName($str));
			return unserialize($str);

		}
		/**
		 *
		 */
		function getTasks(){
			$adb = $this->adb;
			$result = $adb->query("select task from com_vtiger_workflowtasks");
			return $this->getTasksForResult($result);
		}
		
		
		function getTaskTypes(){
			return array("VTEmailTask", "VTEntityMethodTask", "VTCreateTodoTask", 
									 "VTCreateEventTask");
		}
		
		private function getTasksForResult($result){
			$adb = $this->adb;
			$it = new SqlResultIterator($adb, $result);
			$tasks = array();
			foreach($it as $row){
				$text = $row->task;
				
				$this->requireTask(self::taskName($text));
				$tasks[] = unserialize($text);
			}
			return $tasks;
		}
		
		private function taskName($serializedTask){
			$matches = array();
			preg_match ('/"([^"]+)"/', $serializedTask, $matches);
			return $matches[1];
		}
		
		private function requireTask($taskType){
			require_once("tasks/".$taskType.".inc");
		}
		
		
		
	}
	
	abstract class VTTask{
		public abstract function doTask($data);
		public abstract function getFieldNames();
	}
	
	//require 'modules/Workflow/tasks/VTEmailTask.inc';
?>