<?php

function searchByStudent($studentId, $date=''){
	global $db, $data_validation;

	$sql['studentId'] = $data_validation->escape_sql($studentId);
	$sql['date'] = $data_validation->escape_sql($date);
	$output = '';

	if(strlen($sql['date'])){
		$sql = 'SELECT * FROM log WHERE ( studentId LIKE \'%'.$sql['studentId'].'%\' OR firstName LIKE \'%'.$sql['studentId'].'%\' OR lastName LIKE \'%'.$sql['studentId'] . '%\' ) AND date = \''.$sql['date'].'\' ORDER BY date DESC';
	}else{
		$sql = 'SELECT * FROM log WHERE (studentId LIKE \'%'.$sql['studentId'].'%\' OR firstName LIKE \'%'.$sql['studentId'].'%\' OR lastName LIKE \'%'.$sql['studentId'] . '%\') ORDER BY date DESC';
	}

	$db->query($sql, 'searchByStudentLog');
	if($db->num_rows('searchByStudentLog')){
		$output.='<div class="ui-grid-d">
					<div class="ui-block-a"><div class="ui-bar ui-bar-b" style="height:60px">Student</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-b" style="height:60px">Teacher</div></div>
					<div class="ui-block-c"><div class="ui-bar ui-bar-b" style="height:60px">Date</div></div>
					<div class="ui-block-d"><div class="ui-bar ui-bar-b" style="height:60px">Time-In</div></div>
					<div class="ui-block-e"><div class="ui-bar ui-bar-b" style="height:60px">Time-Out</div></div>';

		while($row = $db->fetch_array('searchByStudentLog')){
			$html['firstName'] 		= $data_validation->escape_html($row['firstName']);
			$html['lastName'] 		= $data_validation->escape_html($row['lastName']);
			$html['teacherName'] 	= $data_validation->escape_html($row['teacherName']);
			$html['date'] 			= $data_validation->escape_html(date('n/j/Y', strtotime($row['date'])));
			$html['timeIn'] 		= $data_validation->escape_html(date('g:ia', strtotime($row['timeIn'])));
			$html['timeOut'] 		= ($row['timeOut'] == NULL ? 'Status: Checked In' : $data_validation->escape_html(date('g:ia', strtotime($row['timeOut']))));

			$output .= '	<div class="ui-block-a"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['firstName'].' '.$html['lastName'].'</div></div>
							<div class="ui-block-b"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['teacherName'].'</div></div>
							<div class="ui-block-c"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['date'].'</div></div>
							<div class="ui-block-d"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['timeIn'].'</div></div>
							<div class="ui-block-e"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['timeOut'].'</div></div>';

		}
		$output .= '</div>';
	}else{
		$output .= 'No results were found on the date chosen.';
	}
	return $output;

}

function searchByTeacher($teacherName='', $date=''){
	global $db, $data_validation;

	$sql['teacherName']	= $data_validation->escape_sql($teacherName);
	$sql['date'] 		= $data_validation->escape_sql($date);
	$output = '';

	if(strlen($sql['date'])){
		$db->query('SELECT * FROM log WHERE teacherName LIKE \'%'.$sql['teacherName'].'%\' AND date = \''.$sql['date'].'\' ORDER BY teacherName ASC, date DESC', 'searchByTeacherLog');
	}else{
		$db->query('SELECT * FROM log WHERE teacherName LIKE \'%'.$sql['teacherName'].'%\' ORDER BY teacherName ASC, date DESC', 'searchByTeacherLog');
	}

	if($db->num_rows('searchByTeacherLog')){
		$output .= '<div class="ui-grid-d">
					<div class="ui-block-a"><div class="ui-bar ui-bar-b" style="height:60px">Teacher</div></div>
					<div class="ui-block-c"><div class="ui-bar ui-bar-b" style="height:60px">Date</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-b" style="height:60px">Student</div></div>
					<div class="ui-block-d"><div class="ui-bar ui-bar-b" style="height:60px">Time-In</div></div>
					<div class="ui-block-e"><div class="ui-bar ui-bar-b" style="height:60px">Time-Out</div></div>';
		while($row = $db->fetch_array('searchByTeacherLog')){
			$html['firstName'] 		= $data_validation->escape_html($row['firstName']);
			$html['lastName'] 		= $data_validation->escape_html($row['lastName']);
			$html['teacherName'] 	= $data_validation->escape_html($row['teacherName']);
			$html['date'] 			= $data_validation->escape_html(date('n/j/Y', strtotime($row['date'])));
			$html['timeIn'] 		= $data_validation->escape_html(date('g:ia', strtotime($row['timeIn'])));
			$html['timeOut'] 		= ($row['timeOut'] == NULL ? 'Status: Checked In' : $data_validation->escape_html(date('g:ia', strtotime($row['timeOut']))));

			$output .= '	<div class="ui-block-a"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['teacherName'].'</div></div>
							<div class="ui-block-b"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['date'].'</div></div>
							<div class="ui-block-c"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['firstName'].' '.$html['lastName'].'</div></div>
							<div class="ui-block-d"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['timeIn'].'</div></div>
							<div class="ui-block-e"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['timeOut'].'</div></div>';

		}
		$output .= '</div>';
	}else{
		$output .= 'No results were found on the date chosen.';
	}
	return $output;
}

function searchByDate($date){

	global $db, $data_validation;

	$sql['date'] 		= $data_validation->escape_sql($date);
	$output = '';
	$db->query('SELECT * FROM log WHERE date = \''.$sql['date'].'\' ORDER BY date DESC', 'searchByDateLog');

	$numberOfStudents = $db->num_rows('searchByDateLog');

	if($numberOfStudents > 0){
		$output.='<div>Total number of students on this date = '.$numberOfStudents.'.</div>
					<div class="ui-grid-d">
					<div class="ui-block-a"><div class="ui-bar ui-bar-b" style="height:60px">Date</div></div>
					<div class="ui-block-b"><div class="ui-bar ui-bar-b" style="height:60px">Student</div></div>
					<div class="ui-block-c"><div class="ui-bar ui-bar-b" style="height:60px">Teacher</div></div>
					<div class="ui-block-d"><div class="ui-bar ui-bar-b" style="height:60px">Time-In</div></div>
					<div class="ui-block-e"><div class="ui-bar ui-bar-b" style="height:60px">Time-Out</div></div>';

		while($row = $db->fetch_array('searchByDateLog')){
			$html['firstName'] 		= $data_validation->escape_html($row['firstName']);
			$html['lastName'] 		= $data_validation->escape_html($row['lastName']);
			$html['teacherName'] 	= $data_validation->escape_html($row['teacherName']);
			$html['date'] 			= $data_validation->escape_html(date('n/j/Y', strtotime($row['date'])));
			$html['timeIn'] 		= $data_validation->escape_html(date('g:ia', strtotime($row['timeIn'])));
			$html['timeOut'] 		= ($row['timeOut'] == NULL ? 'Status: Checked In' : $data_validation->escape_html(date('g:ia', strtotime($row['timeOut']))));

			$output .= '	<div class="ui-block-a"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['date'].'</div></div>
							<div class="ui-block-b"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['firstName'].' '.$html['lastName'].'</div></div>
							<div class="ui-block-c"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['teacherName'].'</div></div>
							<div class="ui-block-d"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['timeIn'].'</div></div>
							<div class="ui-block-e"><div class="ui-bar ui-bar-c" style="height:30px">'.$html['timeOut'].'</div></div>';

		}
		$output .= '</div>';
	}else{
		$output .= 'No results were found on the date chosen.';
	}

	return $output;
}
