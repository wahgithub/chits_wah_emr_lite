<?
  class widgets{
  
    function widgets(){
      $this->appName="CHITS Query Browser";
      $this->appVersion="0.17";
      $this->appAuthor="Alison Perez";
    }
  
    function query_class($dbquery,$classification,$query){
	  mysql_select_db($dbquery);
	  $query_cat = mysql_query("SELECT cat_id, cat_label FROM ques_cat ORDER by cat_label ASC") or die(mysql_error());
	

	  if(mysql_num_rows($query_cat)!=0):

	  echo "<table bgcolor='#CCCCCC'>";
          echo "<tr><td colspan=\"2\" style=\"background-color: #666666;color: #FFFF66;text-align: center;font-weight: bold\">Select Classification</td></tr>";
	  echo "<tr>";
	  echo "<td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Classification</td>";

  	  echo "<form action=\"$_SERVER[PHP_SELF]\" name=\"form_cat\" method=\"post\">";
	  echo "<td><select size=\"1\" name=\"sel_class\" onChange=\"autoSubmit();\">";
	  echo "<option value=\"0\">---SELECT CATEGORY---</option>";
	  while($res_ques=mysql_fetch_array($query_cat)){
		if($res_ques[cat_id]==$classification):
			echo "<option value=\"$res_ques[cat_id]\" SELECTED>$res_ques[cat_label]</option>";
		else:
			echo "<option value=\"$res_ques[cat_id]\">$res_ques[cat_label]</option>";
		endif;
	  }

	  echo "</select>";
  	  echo "</form>";

	  echo "</td></tr>";
	
	  if(isset($classification)):
	  
	  $query_ques = mysql_query("SELECT ques_id, ques_label, report_type FROM question WHERE cat_id='$classification' AND visible='Y' ORDER by ques_label ASC") or die("Cannot query: 40");

			  if(mysql_num_rows($query_ques)!=0):
			  
				echo "<tr>";
				echo "<td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Queries</td>";

				echo "<form action=\"$_SERVER[PHP_SELF]\" name=\"form_ques\" method=\"post\">";
				echo "<td><select size=\"1\" name=\"sel_ques\" onChange=\"submitQues();\">";
				echo "<option value=\"0\">--SELECT QUERY--</option>";

			  while($res_ques=mysql_fetch_array($query_ques)){
				
				if($res_ques[ques_id]==$query):
					echo "<option value=\"$res_ques[ques_id]\" SELECTED>$res_ques[ques_label]</option>";
				else:
					echo "<option value=\"$res_ques[ques_id]\">$res_ques[ques_label]</option>";
				endif;
			  }

			  echo "</select>";
			  echo "</form>";

			  echo "</td></tr>";	  

			  endif;	  
	  endif;

	  

      echo "</table>";
	  endif;
    }  


	function query_cat($dbname,$dbname2,$psdate,$pedate,$pbrgy,$facility_id){

	  mysql_select_db($dbname2);
	  $query_cat = mysql_query("SELECT cat_label FROM ques_cat WHERE cat_id='$_SESSION[cat]'") or die("Cannot query: 77");
	  $query_ques = mysql_query("SELECT ques_label FROM question WHERE ques_id='$_SESSION[ques]'") or die("Cannot query: 78");
	  

	  $res_cat = mysql_fetch_array($query_cat);
	  $res_ques = mysql_fetch_array($query_ques);


	  
	  mysql_select_db($dbname);
	  echo "<form action=\"$_SERVER[PHP_SELF]\" method=\"POST\" name=\"form_query\">";
	  echo "<input type=\"hidden\" name=\"ques_name\" value=\"$res_ques[ques_label]\"></input>";

	  echo "<table bgcolor='#CCCCCC'>";
	  echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;font-weight: bold\" colspan='2'>&nbsp;&nbsp;Set Filters ($res_ques[ques_label])&nbsp;&nbsp;</td></tr>";		
        
		$query_brgy = mysql_query("SELECT barangay_id,barangay_name FROM m_lib_barangay ORDER by barangay_name ASC") or die(mysql_error());

		$set_filter = $this->get_filter();
		
		if($set_filter == 1): // start date, end date and barangay dropdown list


		echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Start Date (mm/dd/yyyy)</td>";
		echo "<td><input name=\"sdate\" type=\"text\" size=\"12\" maxlength=\"10\" value=\"$psdate\"></input>";		
		echo "<a href=\"javascript:show_calendar4('document.form_query.sdate', document.form_query.sdate.value);\"><img src='../images/cal.gif' width='16' height='16' border='0' alt='Click Here to Pick up the date'></a>";
		echo "</td></tr>";
        echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">End Date (mm/dd/yyyy)</td>";
        echo "<td><input name=\"edate\" type=\"text\" size=\"12\" maxlength=\"10\" value=\"$pedate\">";
		echo "<a href=\"javascript:show_calendar4('document.form_query.edate', document.form_query.edate.value);\"><img src='../images/cal.gif' width='16' height='16' border='0' alt='Click Here to Pick up the date'></a>";		
		echo "</td></tr>";
		echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Barangay</td>";

		if(mysql_num_rows($query_brgy)!=0):        
		        echo "<td><select name=\"sel_brgy\" size=\"1\">";
                        echo "<option value=\"all\">All Barangay</option>";
                                while($res_brgy=mysql_fetch_array($query_brgy)){                  
                                        if($pbrgy==$res_brgy[barangay_id]):
                                                echo "<option value=\"$res_brgy[barangay_id]\" SELECTED>$res_brgy[barangay_name]</option>";
                                        else:
                                                echo "<option value=\"$res_brgy[barangay_id]\">$res_brgy[barangay_name]</option>";
                                        endif;
                                }
          
                        echo "</select></td>";
                else:
                        echo "<td>No barangays found</td>";
                endif;

                echo "</tr>";

		elseif($set_filter=='3'):
		        $this->disp_filter_quarterly($query_brgy,$facility_id);
                elseif($set_filter=='4'):
                        $this->disp_filter_monthly($query_brgy,$facility_id);
                elseif($set_filter=='5'):
                        $this->disp_filter_weekly($query_brgy,$facility_id);
                elseif($set_filter=='6'):
                        $this->disp_filter_annual($query_brgy,$facility_id);
		else:
			$this->disp_filter_form2($query_brgy,$facility_id);
		endif;

        //$this->additional_filter($_SESSION["ques"],"FP Methods");
	$this->additional_filter($_SESSION["ques"]);

        echo "<tr align=\"center\">";
        echo "<td colspan=\"2\"><input type=\"submit\" name=\"q_submit\" value=\"Submit\" target=\"new\"></input>&nbsp;&nbsp;&nbsp;";
        echo "<input type=\"reset\" name=\"q_reset\" value=\"Reset\"></input></td>";
        echo "</tr>";

      echo "</table>";

    } 

	function get_filter(){ //set filter determines what date and barangay form shall be displayed. summary tables usually uses checkbox for brgy while tcl's are using dropdown list

                $q_type = mysql_query("SELECT report_type FROM question WHERE ques_id='$_SESSION[ques]'") or die("Cannot query (147)".mysql_error());
                list($report_type) = mysql_fetch_array($q_type);

 		if($report_type=='S'): //for other question codes, just add || here. this is for summary tables.
			$_SESSION[filter] = 2;
                elseif($report_type=='Q'):
                        $_SESSION[filter] = 3;
                elseif($report_type=='M'):
                        $_SESSION[filter] = 4;
                elseif($report_type=='W'):
                        $_SESSION[filter] = 5;
                elseif($report_type=='A'):
                        $_SESSION[filter] = 6;
		else:
			$_SESSION[filter] = 1;
		endif;
		
                return $_SESSION[filter];	
	}


	function disp_filter_form2($q_brgy){
		$buwan_label = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June',07=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');

		$buwan = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');

		$_SESSION[months] = $buwan_label;

		echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Start Month</td>";

		echo "<td>";
		echo "<select name='smonth' size='1'>";
		foreach($buwan as $key=>$value){
			echo "<option value='$key'>$value</option>";	
		}
		echo "</select>";
		echo "</td></tr>";


		echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">End Month</td>";
		echo "<td>";
		echo "<select name='emonth' size='1'>";
		foreach($buwan as $key=>$value){
			echo "<option value='$key'>$value</option>";	
		}
		echo "</select>";
		echo "</td></tr>";


		echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Year</td>";
		
		echo "<td><select name='year' size='1'>";

		for($i = (date('Y')-5);$i< (date('Y')+5);$i++){					
			if($i==date('Y')):
				echo "<option value='$i' selected>$i</option>";
			else:
				echo "<option value='$i'>$i</option>";
			endif;
		}
		echo "</select></td></tr>";
		
        $this->checkbox_brgy($q_brgy);				
	}
	
	function additional_filter($ques_id){

        if($ques_id==40): //if the query is about FP TCL, display another list showing the FP methods
                $q_fp_method = mysql_query("SELECT method_name, method_id, fhsis_code FROM m_lib_fp_methods ORDER by method_name ASC") or die("Cannot query: Check FP tables");
	
                if(mysql_num_rows($q_fp_method)!=0):
                        echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">FP Methods</td>";
                        echo "<td><select name='sel_fp_method' size='1'>";
                        while(list($method_name,$method_id,$fhsis_code) = mysql_fetch_array($q_fp_method)){
                                echo "<option value='$method_id'>$method_name ($fhsis_code)</option>";
                        }
                        echo "</select></td>";
                        echo "</tr>";
                        $this->show_year();
                endif;
	elseif($ques_id==156): //lab exams
		echo "<tr>";	
		echo "<td style='background-color: #666666;color: #FFFF66;text-align: center;'>Laboratory Exams</td><td><select name='sel_lab' size='1'>";
		echo "<option value='all'>All laboratory exams</option>";
		echo "<option value='m_consult_lab_fecalysis'>Fecalysis</option>";
		echo "<option value='m_consult_lab_hematology'>Hematology</option>";
		echo "<option value='m_consult_lab_sputum'>Sputum Exam</option>";
		echo "<option value='m_consult_lab_urinalysis'>Urinalysis</option>";
		echo "</select></td>";
		echo "</tr>";

	elseif($ques_id==74): 
		if($_SESSION["icd_level"]=='main'):
			$main = 'SELECTED';
		elseif($_SESSION["icd_level"]=='exact'):
			$exact = 'SELECTED';
		else:
			$main = $exact = '';
		endif;
		
		$q_icd = mysql_query("SELECT class_id, class_name, icd10 FROM m_lib_notes_dxclass WHERE morbidity='Y' ORDER by class_name ASC, icd10 ASC") or die("Cannot query 245: ".mysql_error());


		echo "<tr>";
		echo "<td style='background-color: #666666;color: #FFFF66;text-align: center;'>Select Morbidity Disease</td><td><select name='sel_morbidity' size='1'>";
		
		while(list($class_id,$class_name,$icd10)=mysql_fetch_array($q_icd)){
			if($_SESSION["morbidity_code"]==$class_id):
				echo "<option value='$class_id' SELECTED>$class_name ($icd10)</option>";
			else:
				echo "<option value='$class_id'>$class_name ($icd10)</option>";
			endif;
		}
		
		echo "</select></td></tr>";

		
		echo "<tr>";
		echo "<td style='background-color: #666666;color: #FFFF66;text-align: center;'>ICD10 level</td><td><select name='sel_icd_level' size='1'>";
		
		echo "<option value='main' $main>Main ICD10 code (i.e. A09)</option>";
		echo "<option value='exact' $exact>Specific ICD10 code (i.e. A09.1)</option>";
		
		echo "</select></td></tr>";
	else:

         endif;
	}
	
	function disp_filter_quarterly($q_brgy){
	                echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Quarter</td>";
	                echo "<td><select name='sel_quarter' size='1'>";
	                        for($i=1;$i<5;$i++){
                                        echo "<option value='$i'>$i</option>";
	                        }
	                echo "</select></td></tr>";
	                $this->show_year();
                        $this->checkbox_brgy($q_brgy);
	}
	
	function disp_filter_monthly($q_brgy,$facility_id){

	        $buwan_label = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June',07=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
                $buwan = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
		$_SESSION[months] = $buwan_label;

		echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Month</td>";

		echo "<td>";
		echo "<select name='smonth' size='1'>";
		foreach($buwan as $key=>$value){
			if($key==$_POST["smonth"]):
				echo "<option value='$key' SELECTED>$value</option>";	
			else:
				echo "<option value='$key'>$value</option>";
			endif;
		}
		echo "</select>";
		echo "</td></tr>";

		$this->show_year();		
		$this->checkbox_brgy($q_brgy,$facility_id);		
	}
	
	function disp_filter_weekly($q_brgy,$facility_id){
	        $this->disp_week();
	        $this->show_year();
	        $this->checkbox_brgy($q_brgy,$facility_id);
	}
		
	
	function disp_week($q_brgy,$facility_id){
	        echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Week</td>";
                echo "<td><select name='sel_week' value='1'>";
	        
	        for($i=1;$i<53;$i++){
	                echo "<option value='$i'>$i</option>";
	        }
	        
	        echo "</select></td></tr>";
	}
	
	function disp_filter_annual($q_brgy,$facility_id){
	        $this->show_year();
            $this->checkbox_brgy($q_brgy,$facility_id);
	}

	
	function checkbox_brgy($q_brgy,$facid){		
		
		//print_r($_POST);

		if($_POST["sel_facility"]!=''):

			$q_doh = mysql_query("SELECT doh_class_id FROM m_lib_health_facility WHERE facility_id='$_POST[sel_facility]'") or die("Cannot query 342: ".mysql_error());
			list($doh_facid) = mysql_fetch_array($q_doh);

			$_SESSION["new_facility_code"] = $doh_facid;
			$arr_brgy = array();
			$q_brgy_id = mysql_query("SELECT barangay_id FROM m_lib_health_facility_barangay WHERE facility_id='$_POST[sel_hidden_value]'") or die("Cannot query 341: ".mysql_error());

			while(list($brgyid)=mysql_fetch_array($q_brgy_id)){
				array_push($arr_brgy,$brgyid);
			}
		else:
			$_SESSION["new_facility_code"] = '';
		endif;



		$q_health_facility = mysql_query("SELECT DISTINCT a.facility_id, b.facility_name,b.doh_class_id FROM m_lib_health_facility_barangay a, m_lib_health_facility b WHERE a.facility_id=b.facility_id ORDER by b.facility_name ASC") or die("Cannot query 340: ".mysql_error());

        echo "<tr><td valign='top' style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Health Facility</td><td>";
		
		echo "<input name='sel_hidden_value' type='hidden' value=''></input>";
		echo "<select name='sel_facility' value='1' onChange='check_facility();'>";
		echo "<option value=''>--- Select Health Facility ---</option>";
		while(list($facility_id,$facility_name)=mysql_fetch_array($q_health_facility)){


			if($_POST["sel_facility"]==$facility_id):
				echo "<option value='$facility_id' SELECTED>$facility_name</option>";			
			else:
				echo "<option value='$facility_id'>$facility_name</option>";
			endif;
		}
		echo "</select>";

		echo "</td></tr>";
				

        echo "<tr><td valign='top' style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Barangay</td><td>";
		
		if(count($arr_brgy)==0):
			echo "<input type='checkbox' name='brgy[]' value='all' checked>All</input>&nbsp;";
		else:
			echo "<input type='checkbox' name='brgy[]' value='all'>All</input>&nbsp;";
		endif;

		$counter = 1;
		while(list($brgyid,$brgyname)=mysql_fetch_array($q_brgy)){
			if(in_array($brgyid,$arr_brgy)):
				echo "<input type='checkbox' name='brgy[]' value='$brgyid' CHECKED>$brgyname</input>&nbsp;";
			else:
				echo "<input type='checkbox' name='brgy[]' value='$brgyid'>$brgyname</input>&nbsp;";
			endif;

			$counter++;
			if(($counter%4)==0):
				echo "<br>";
			endif;
		}
		echo "</td></tr>";			
	}
	

	function show_year(){
	        echo "<tr><td style=\"background-color: #666666;color: #FFFF66;text-align: center;\">Year</td>";
		
		echo "<td><select name='year' size='1'>";

		for($i = (date('Y')-5);$i< (date('Y')+5);$i++){	
			
			if($_POST["year"]!=''):
				if($_POST["year"]==$i):
					echo "<option value='$i' selected>$i</option>";
				else:
					echo "<option value='$i'>$i</option>";
				endif;
			else:
				if($i==date('Y')):
					echo "<option value='$i' selected>$i</option>";
				else:
					echo "<option value='$i'>$i</option>";
				endif;
			endif;
		}
		echo "</select></td></tr>";
	}


	function footer(){
		echo "<br><br><br>";
      		echo "<table width='100%' style='font-family: arial; font-size: 11px;'>";
		echo "<tr><td height='300;'></td></tr>";
      		echo "<tr align='center'><td valign='top' style=\"background-color: #666666;color: #FFFF66;text-align: center;\">";
      		echo "Copyright @ 2007-2012 | The Query Browser | ";
      		echo "Developed and maintained by alison@perez-ph.net";
      		echo "</td></tr>";
      		echo "</table>";
	}	
  }
?>