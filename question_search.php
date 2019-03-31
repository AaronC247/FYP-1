<?php

$db = pg_connect("host=localhost port=5432 dbname=Maptest user=postgres password=");


//array used to store the geojson output
$geoJSON_array =array();
//array used to sort all scores 
$sort_scores=array();

//Housing Querys
$housing_q1 = $_POST['housing_q1'];
$housing_query_1 ="select gid, T6_1_HB_H, T6_1_FA_H, T6_1_BS_H, T6_1_CM_H, T6_1_NS_H, T6_1_TH from census_2016_data order by gid;";
$housing_result_1 = pg_query($housing_query_1); 

$housing_q2 = $_POST['housing_q2'];
$housing_query_2 = "SELECT gid,T6_2_PRE19H,T6_2_19_45H,T6_2_46_60H,T6_2_61_70H,T6_2_71_80H,T6_2_81_90H,T6_2_91_00H,T6_2_01_10H,T6_2_11LH,T6_2_NSH,T6_2_TH FROM census_2016_data order by gid;"; 
$housing_result_2 = pg_query($housing_query_2); 

$housing_q3 = $_POST['housing_q3'];
$housing_query_3 ="Select gid, T6_4_1RH, T6_4_2RH, T6_4_3RH, T6_4_4RH, T6_4_5RH, T6_4_6RH, T6_4_7RH, T6_4_GE8RH, T6_4_NSH, T6_4_TH from census_2016_data order by gid;";
$housing_result_3 = pg_query($housing_query_3); 

$housing_q4 = $_POST['housing_q4'];
$housing_query_4 ="select gid, T6_3_OMLH, T6_3_OOH, T6_3_RPLH, T6_3_RLAH, T6_3_RVCHBH, T6_3_OFRH, T6_3_NSH, T6_3_TH from census_2016_data order by gid;";
$housing_result_4 = pg_query($housing_query_4); 

$housing_q5 = $_POST['housing_q5'];
$housing_query_5 ="select gid, avg_price from house_prices_gid order by gid;";
$housing_result_5 = pg_query($housing_query_5); 

//extra housing queries
//if the extra section is opened in the questionaire then
//the value is 1 and the extra quries are ran
//otherwise the values are assigned 0 so they wont effect
//the overall weighting of inputs
$housing_extra = $_POST['housing_extra'];
if($housing_extra == 1){
	$housing_extra_1 = $_POST['housing_extra_1'];
	$housing_extra_query_1 ="select gid, T6_5_NCH,T6_5_OCH,T6_5_NGCH,T6_5_ECH,T6_5_CCH,T6_5_PCH,T6_5_LPGCH,T6_5_WCH,T6_5_OTH,T6_5_NS,T6_5_T from census_2016_data order by gid;";
	$housing_extra_result_1 = pg_query($housing_extra_query_1);
	
	$housing_extra_2 = $_POST['housing_extra_2'];
	$housing_extra_query_2 ="select gid, T6_6_PM,T6_6_GSLA,T6_6_GSP,T6_6_OP,T6_6_N,T6_6_NS,T6_6_T,T6_7_PS from census_2016_data order by gid;";
	$housing_extra_result_2 = pg_query($housing_extra_query_2);

}
else{
	$housing_extra_1 = 0;
	$housing_extra_2 = 0;
}

//Location Querys
$location_q1 = $_POST['location_q1'];
$location_query_1 ="select a.gid, a.T1_1AGETT, b.shape__are from census_2016_data a, census b where a.gid = b.gid order by a.gid;";
$location_result_1 = pg_query($location_query_1); 

$location_q2 = $_POST['location_q2'];
$location_query_2 ="SELECT census_gid,total2011,atmahro,dnao,ko,reho,bro,tro,fdo,cdo,weo,dpe,pooso,gjoo from crime_area_avgs order by census_gid";
$location_result_2 = pg_query($location_query_2); 

$location_q3 = $_POST['location_q3'];
$location_query_3 ="SELECT  gid, T9_1_TT, T8_1_ULGUPJT  FROM census_2016_data order by gid;";
$location_result_3 = pg_query($location_query_3); 

$location_q4 = $_POST['location_q4'];
$location_query_4 ="Select gid, T10_4_ODNDT, T10_4_HDPQT, T10_4_PDT, T10_4_DT, T10_4_NST, T10_4_TT from census_2016_data order by gid;";
$location_result_4 = pg_query($location_query_4); 

$location_q5 = $_POST['location_q5'];
$location_query_5 ="Select gid, T9_1_PWT, T9_1_MTT, T9_1_TT from census_2016_data order by gid;";
$location_result_5 = pg_query($location_query_5); 

$location_extra = $_POST['location_extra'];
if($location_extra == 1){
	$location_extra_1 = $_POST['location_extra_1'];
	$location_extra_query_1 ="select gid, T15_3_B,T15_3_OTH,T15_3_N,T15_3_NS,T15_3_T from census_2016_data order by gid;";
	$location_extra_result_1 = pg_query($location_extra_query_1);
	
	$location_extra_2 = $_POST['location_extra_2'];
	$location_extra_query_2 ="Select gid, T9_2_PI, T9_2_PJ,T9_2_PE,T9_2_PF,T9_2_PG,T9_2_PZ,T9_2_PT from census_2016_data order by gid;";
	$location_extra_result_2 = pg_query($location_extra_query_2);

}
else{
	$location_extra_1 = 0;
	$location_extra_2 = 0;
}

//Healthcare Querys
$healthcare_q1 = $_POST['healthcare_q1'];
$healthcare_q1 = invert_distance_score($healthcare_q1);
$healthcare_query_1 ="SELECT gid, distance FROM public.hos_dis Order By gid";
$healthcare_result_1 = pg_query($healthcare_query_1); 

$healthcare_q2 = $_POST['healthcare_q2'];
$healthcare_q2 = invert_distance_score($healthcare_q2);
$healthcare_query_2 ="SELECT gid, distance FROM public.doc_dist Order By gid";
$healthcare_result_2 = pg_query($healthcare_query_2); 

$healthcare_q3 = $_POST['healthcare_q3'];
$healthcare_q3 = invert_distance_score($healthcare_q3);
$healthcare_query_3 ="SELECT gid, distance FROM public.pharm_dist Order By gid";
$healthcare_result_3 = pg_query($healthcare_query_3); 

$healthcare_extra = $_POST['healthcare_extra'];
if($healthcare_extra == 1){
	$healthcare_extra_1 = $_POST['healthcare_extra_1'];
	$healthcare_extra_1 = invert_distance_score($healthcare_extra_1);
	$healthcare_extra_query_1 ="SELECT gid, distance FROM public.dent_dist Order By gid";
	$healthcare_extra_result_1 = pg_query($healthcare_extra_query_1);
	
	$healthcare_extra_2 = $_POST['healthcare_extra_2'];
	$healthcare_extra_2 = invert_distance_score($healthcare_extra_2);
	$healthcare_extra_query_2 ="SELECT gid, distance FROM public.nursing_home_dist Order By gid";
	$healthcare_extra_result_2 = pg_query($healthcare_extra_query_2);
	
}
else{
	$healthcare_extra_1 = 0;
	$healthcare_extra_2 = 0;
}


//Education Querys
$education_q1 = $_POST['education_q1'];
$education_query_1 ="Select gid, count from public.schools_count order by gid;";
$education_result_1 = pg_query($education_query_1); 

$education_q2 = $_POST['education_q2'];
$education_q2 = invert_distance_score($education_q2);
$education_query_2 ="SELECT gid, distance FROM public.uni_dist Order By gid";
$education_result_2 = pg_query($education_query_2); 

$education_extra = $_POST['education_extra'];
if($education_extra == 1){
	$education_extra_1 = $_POST['education_extra_1'];
	$education_extra_query_1 ="Select gid, count from preschool_count order by gid;";
	$education_extra_result_1 = pg_query($education_extra_query_1);
	
	$education_extra_2 = $_POST['education_extra_2'];
	$education_extra_query_2 ="Select gid, count from library_count order by gid;";
	$education_extra_result_2 = pg_query($education_extra_query_2);
	
}
else{
	$education_extra_1 = 0;
	$education_extra_2 = 0;
}


//amenities Querys 
$amenities_q1 = $_POST['amenities_q1'];
$amenities_query_1 ="Select gid, count from lesiure_table order by gid;";
$amenities_result_1 = pg_query($amenities_query_1); 

$amenities_q2 = $_POST['amenities_q2'];
$amenities_query_2 ="Select gid, count from activity_table order by gid;";
$amenities_result_2 = pg_query($amenities_query_2); 

$amenities_q3 = $_POST['amenities_q3'];
$amenities_query_3 ="Select gid, count from rest_and_bars_count order by gid;";
$amenities_result_3 = pg_query($amenities_query_3); 

$amenities_q4 = $_POST['amenities_q4'];
$amenities_query_4 ="Select gid, count from groce_stores_count order by gid;";
$amenities_result_4 = pg_query($amenities_query_4); 

$amenities_q5 = $_POST['amenities_q5'];
$amenities_query_5 ="Select gid, count from retail_stores_count order by gid;";
$amenities_result_5 = pg_query($amenities_query_5); 

$amenities_q6 = $_POST['amenities_q6'];
$amenities_query_6 ="Select gid, count from tourism_count order by gid;";
$amenities_result_6 = pg_query($amenities_query_6); 

$amenities_extra = $_POST['amenities_extra'];
if($amenities_extra == 1){
	$amenities_extra_1 = $_POST['amenities_extra_1'];
	$amenities_extra_1 = invert_distance_score($amenities_extra_1);
	$amenities_extra_query_1 ="Select gid, distance from fire_station_dist order by gid;";
	$amenities_extra_result_1 = pg_query($amenities_extra_query_1);
	
	$amenities_extra_2 = $_POST['amenities_extra_2'];
	$amenities_extra_2 = invert_distance_score($amenities_extra_2);
	$amenities_extra_query_2 ="Select gid, distance from post_office_dist order by gid;";
	$amenities_extra_result_2 = pg_query($amenities_extra_query_2);
	
}
else{
	$amenities_extra_1 = 0;
	$amenities_extra_2 = 0;
}


//People Querys
$people_q1 = $_POST['people_q1'];
$people_query_1 ="SELECT gid, T3_2ALLT, T1_2T FROM census_2016_data order by gid";
$people_result_1 = pg_query($people_query_1); 

$people_q2 = $_POST['people_q2'];
$people_query_2 ="Select gid, T8_1_WT, T8_1_LFFJT, T8_1_ULGUPJT, T8_1_ST, T8_1_LAHFT, T8_1_RT, T8_1_UTWSDT, T8_1_OTHT, T8_1_TT from census_2016_data order by gid;";
$people_result_2 = pg_query($people_query_2); 
$people_q2_a = $_POST['people_q2_a'];
$people_q2_b = $_POST['people_q2_b'];
$people_q2_c = $_POST['people_q2_c'];

$people_q3 = $_POST['people_q3'];
$people_query_3 ="Select gid, T2_2WI,T2_2WIT,T2_2OW,T2_2BBI,T2_2AAI,T2_2OTH,T2_2NS,T2_2T from census_2016_data order by gid;";
$people_result_3 = pg_query($people_query_3); 
$people_q3_a = $_POST['people_q3_a'];
$people_q3_b = $_POST['people_q3_b'];
$people_q3_c = $_POST['people_q3_c'];
$people_q3_d = $_POST['people_q3_d'];
$people_q3_e = $_POST['people_q3_e'];
$people_q3_f = $_POST['people_q3_f'];

$people_q4 = $_POST['people_q4'];
$people_query_4 ="Select gid, T2_4CA,T2_4OR,T2_4NR,T2_4NS,T2_4T from census_2016_data order by gid;";
$people_result_4 = pg_query($people_query_4); 
$people_q4_a = $_POST['people_q4_a'];
$people_q4_b = $_POST['people_q4_b'];
$people_q4_c = $_POST['people_q4_c'];

$people_extra = $_POST['people_extra'];
if($people_extra == 1){
	$people_extra_1 = $_POST['people_extra_1'];
	$people_extra_query_1 ="Select gid, T12_3_VGT,T12_3_GT,T12_3_NST,T12_3_TT from census_2016_data order by gid;";
	$people_extra_result_1 = pg_query($people_extra_query_1);
}
else{
	$people_extra_1 = 0;
}

//transport queries
$transport_q1 = $_POST['transport_q1'];
$transport_query_1 ="SELECT gid, T11_3_D1,T11_3_D2,T11_3_D3,T11_3_D4,T11_3_D5,T11_3_D6,T11_3_NS,T11_3_T FROM census_2016_data order by gid";
$transport_result_1 = pg_query($transport_query_1); 

$transport_q2 = $_POST['transport_q2'];
$transport_query_2 ="SELECT gid, T11_1_BUT, T11_1_TDLT, T11_1_TT FROM census_2016_data order by gid";
$transport_result_2 = pg_query($transport_query_2); 

$transport_q3 = $_POST['transport_q3'];
$transport_query_3 ="Select gid, count from trans_count order by gid;";
$transport_result_3 = pg_query($transport_query_3); 

$transport_extra = $_POST['transport_extra'];
if($transport_extra == 1){
	$transport_extra_1 = $_POST['transport_extra_1'];
	$transport_extra_query_1 ="SELECT gid,T15_1_NC,T15_1_1C,T15_1_2C,T15_1_3C,T15_1_GE4C,T15_1_NSC,T15_1_TC FROM census_2016_data order by gid";
	$transport_extra_result_1 = pg_query($transport_extra_query_1);		
}
else{
	$transport_extra_1 = 0;
}


//start array
array_push($geoJSON_array,"{'type': 'FeatureCollection','crs': { 'type': 'name', 'properties': { 'name': 'urn:ogc:def:crs:OGC:1.3:CRS84' } },'features':[");

//as three of the questions are not agree disagree format they have a differnt method for getting the weighting
$non_agree_disagree_question_1= calc_weighting_non_agree_disagree($housing_q1);
$non_agree_disagree_question_2= calc_weighting_non_agree_disagree($housing_q4);
$non_agree_disagree_question_3= calc_weighting_non_agree_disagree($housing_q5);

	//total value of the slider inputs for the weighting calucation
	$housing_input = $non_agree_disagree_question_1+$housing_q2+$housing_q3+$non_agree_disagree_question_2+$non_agree_disagree_question_3;
	$location_input = $location_q1+$location_q2+$location_q3+$location_q4+$location_q5;
	$healthcare_input = $healthcare_q1+$healthcare_q2+$healthcare_q3;
	$education_input = $education_q1+$education_q2;
	$amenities_input = $amenities_q1+$amenities_q2+$amenities_q3+$amenities_q4+$amenities_q5+$amenities_q6;
	$people_input = $people_q1+$people_q2+$people_q3+$people_q4;
	$transport_input = $transport_q1+$transport_q2+$transport_q3;
	$extra_input = $housing_extra_1 + $housing_extra_2 + $location_extra_1 + $location_extra_2 + $healthcare_extra_1+ $healthcare_extra_2
					+ $education_extra_1 + $education_extra_2 + $amenities_extra_1 + $amenities_extra_2 + $people_extra_1 + $transport_extra_1;
	
	$total_input = $housing_input + $location_input + $healthcare_input + $education_input 
					+ $amenities_input + $people_input + $transport_input + $extra_input;
					
$query ="SELECT gid, countyname, geometry, edname FROM public.census Order By gid";
$result = pg_query($query); 
$num_lines = pg_numrows($result);
$count = 0;
			
//while loop to generate output file
while ($row = pg_fetch_row($result)) {
//	$row = pg_fetch_row($result);
	$query_scores=array();

	///start here /////
	
	$housing_scores=array();
	//house1 - Question 1. What kind of home are you looking for?
	$h1_row = pg_fetch_row($housing_result_1);
	$num_type_of_select_homes = 0;
	
	//as question is a non agree disagree question 
	//the slider has to weight movement towards the left postivily aswell 
	//slider in middle
	if($housing_q1 == 50){
	$home_weight = 1;
	$apt_weight = 1;
	}
	//slider on apparment side
	elseif($housing_q1 < 50){
	$home_weight = ($housing_q1/50.0);
	$apt_weight = 1;
	}
	//slider on house side
	else{
	$home_weight = 1;
	$apt_weight = 2-($housing_q1/50.0);
	}
	
	//number of apartments 
	$apt = $h1_row[2]+$h1_row[3];
	//number of homes
	$home = $h1_row[1];
	
	$num_type_of_select_homes = ($apt*$apt_weight) + ($home*$home_weight);
	//num of all homes is calcualted by taking unwanted homes from the total
	$num_all_homes = $h1_row[6] - $h1_row[5]  - $h1_row[4];
		
	$h1_score = ($num_type_of_select_homes/$num_all_homes)*100;
	
	$weighted_score = calc_weighted_score($non_agree_disagree_question_1,$h1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($housing_scores, $h1_score);

	//house2 - Question 2. A new house is perferable.
	$num_type_of_select_homes = 0;
	//adjusts the types of home added depending on the users input
	//if the user selects a high score the number of new homes are added
	$h2_row = pg_fetch_row($housing_result_2);
	if($housing_q2 <= 100){
	$num_type_of_select_homes += $h2_row[9];
	}if($housing_q2 < 85){
	$num_type_of_select_homes += $h2_row[8];
	}if($housing_q2 < 70){
	$num_type_of_select_homes += $h2_row[7];
	}if($housing_q2 < 60){
	$num_type_of_select_homes += $h2_row[6];
	}if($housing_q2 < 50){
	$num_type_of_select_homes += $h2_row[5];
	}if($housing_q2 < 40){
	$num_type_of_select_homes += $h2_row[4];
	}if($housing_q2 < 30){
	$num_type_of_select_homes += $h2_row[3];
	}if($housing_q2 < 20){
	$num_type_of_select_homes += $h2_row[2];
	}if($housing_q2 < 10){
	$num_type_of_select_homes += $h2_row[1];
	}
	//take non stated homes from total homes
	$num_all_homes = $h2_row[11] - $h2_row[10];
	
	$h2_score = ($num_type_of_select_homes/$num_all_homes)*100;
	$weighted_score = calc_weighted_score($housing_q2,$h2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($housing_scores, $h2_score);
	
	//house3 - Question 3. A large home is desired.

	$num_type_of_select_homes = 0;
	$h3_row = pg_fetch_row($housing_result_3);
	//adjusts the types of home added depending on the users input
	//if the user selects a high score the number of large homes are added
	if($housing_q3 <= 100){
	$num_type_of_select_homes += $h3_row[8];
	}if($housing_q3 < 85){
	$num_type_of_select_homes += $h3_row[7];
	}if($housing_q3 < 70){
	$num_type_of_select_homes += $h3_row[6];
	}if($housing_q3 < 60){
	$num_type_of_select_homes += $h3_row[5];
	}if($housing_q3 < 50){
	$num_type_of_select_homes += $h3_row[4];
	}if($housing_q3 < 40){
	$num_type_of_select_homes += $h3_row[3];
	}if($housing_q3 < 30){
	$num_type_of_select_homes += $h3_row[2];
	}if($housing_q3 < 20){
	$num_type_of_select_homes += $h3_row[1];
	}
	//take non stated homes from total homes
	$num_all_homes = $h3_row[10] - $h3_row[9];
	
	$h3_score = ($num_type_of_select_homes/$num_all_homes)*100;
	
	$weighted_score = calc_weighted_score($housing_q3,$h3_score,$total_input);

	array_push($query_scores, $weighted_score);		
	array_push($housing_scores, $h3_score);

	
	//house 4 - Question 4. Are you looking to rent or buy?

	$h4_row = pg_fetch_row($housing_result_4);

	$num_type_of_select_homes = 0;
	
	//slider in the middle
	if($housing_q4 == 50){
	$buy_weight = 1;
	$rent_weight = 1;
	}
	//slider in on buy side
	elseif($housing_q4 < 50){
	$buy_weight = ($housing_q4/50.0);
	$rent_weight = 1;
	}
	//slider on the rent side
	else{
	$buy_weight = 1;
	$rent_weight = 2-($housing_q4/50.0);
	}
	
	$rented = $h4_row[3]+$h4_row[4]+$h4_row[5];
	$owned = $h4_row[1]+$h4_row[2];
	
	$num_type_of_select_homes = ($rented*$rent_weight) + ($owned*$buy_weight);
	$num_all_homes = $h4_row[8] - $h4_row[7]  - $h4_row[6];
		
	$h4_score = ($num_type_of_select_homes/$num_all_homes)*100;
	
	$weighted_score = calc_weighted_score($non_agree_disagree_question_2,$h4_score,$total_input);

	array_push($query_scores, $weighted_score);
	array_push($housing_scores, $h4_score);

	//house5 - Question 5. Is your budget high or low?
	$h5_row = pg_fetch_row($housing_result_5);
	//calculates the score using a exponetial function, first two values
	//are to do with the line of the function
	$h5_score = calc_exponential(63064.832,1.022318155,$housing_q5,$h5_row[1]);
	
	$weighted_score = calc_weighted_score($non_agree_disagree_question_3,$h5_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($housing_scores, $h5_score);
	
	
	//*************************************************************************************************************
	//Extra Housing Questions
	//extra housing 1 - Extra Question 1. Housing heated by gas or electricity is preferable.
	if($housing_extra == 1){
	$hx1_row = pg_fetch_row($housing_extra_result_1);

	$num_all_homes = $hx1_row[11] - $hx1_row[10];
	$num_selected_homes = $hx1_row[4] + $hx1_row[3];
	$home_percentage = 	($num_selected_homes/$num_all_homes)*100;
	
	$hx1_score = ($home_percentage/$housing_extra_1)*100;
	$hx1_score = cap_score($hx1_score);

	$weighted_score = calc_weighted_score($housing_extra_1,$hx1_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($housing_scores, $hx1_score);
	
	//extra housing 2 - Extra Question 2. Housing with a connection to mains water is preferable
	$hx2_row = pg_fetch_row($housing_extra_result_2);

	$num_all_homes = $hx2_row[7] - $hx2_row[6];
	$num_selected_homes = $hx2_row[1] + $hx2_row[2];
	$home_percentage = 	($num_selected_homes/$num_all_homes)*100;
	
	$hx2_score = ($home_percentage/$housing_extra_2)*100;
	$hx2_score = cap_score($hx2_score);
	$weighted_score = calc_weighted_score($housing_extra_2,$hx2_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($housing_scores, $hx2_score);
	
	}
	
	//=============================================================================================================
	$location_scores=array();

	
	//location1 - Question 6. Are you looking for an area with a high population?
	$l1_row = pg_fetch_row($location_result_1);
	$population = $l1_row[1];
	//convert area to km^2
	$area = $l1_row[2]*1000;
	$pop_density = $population/$area;
	
	$b = pow (1.045312753 , $location_q1 );
	$exp = 18.3764726 * ($b);
	
	$l1_score = ($pop_density/$exp) *100;
	$l1_score = cap_score($l1_score);

	$weighted_score = calc_weighted_score($location_q1,$l1_score,$total_input);

	array_push($query_scores, $weighted_score);	
	array_push($location_scores, $l1_score);


	//location2 - Question 7. A low level in crime is important.
	$l2_row = pg_fetch_row($location_result_2);
	$total_crime = 0;
	$total_pop = $l2_row[1];
	for($i = 2; $i<14; $i++){
		$total_crime += $l2_row[$i];
	}
	$crime_rate = ($total_crime / $total_pop)*100;
		
	$b = pow (1.030729168, $location_q2);
	$exp = 0.9391901019 * ($b);
	$exp = cap_score($exp);

	if($crime_rate >$exp){
		$crime_rate = $exp;
	}
	$l2_score = ($crime_rate/$exp)*100;
	
	$weighted_score = calc_weighted_score($location_q2,$l2_score,$total_input);
	
	array_push($query_scores, $weighted_score);	
	array_push($location_scores, $l2_score);


	//location3 - Question 8. An area with a high employment rate is desirable.
	$l3_row = pg_fetch_row($location_result_3);
	$emp_rate = $l3_row[2]/$l3_row[1];
	$emp_rate = $emp_rate*100;
	$emp_rate = 100 - $emp_rate;
	
	$b = pow (1.002253053, $location_q3);
	$exp = 81.19776942 * ($b);
	$exp = cap_score($exp);
	
	if($emp_rate >$exp){
		$emp_rate = $exp;
	}

	$l3_score = ($emp_rate/$exp)*100;
	
	$weighted_score = calc_weighted_score($location_q3,$l3_score,$total_input);
	
	array_push($query_scores, $weighted_score);	
	array_push($location_scores, $l3_score);

	
	//location4 - Question 9. An area with a high level of education is desirable.
	$l4_row = pg_fetch_row($location_result_4);
	//highly educated 
	$h_educated = $l4_row[1]+$l4_row[2]+$l4_row[3]+$l4_row[4];
	//total educated
	$t_educated = $l4_row[6]-$l4_row[5];
	if($t_educated > 0){
	$h_educated_rate = $h_educated/$t_educated;
	}else{ 
	$h_educated_rate = 0;
	}
	$l4_score = (($h_educated_rate*100)/$location_q4)*100;
	$l4_score = cap_score($l4_score);

	$weighted_score = calc_weighted_score($location_q4,$l4_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($location_scores, $l4_score);
	
	//location5 - Question 10. An area with Professional, managerial and technical workers is desirable.
	$l5_row = pg_fetch_row($location_result_5);
	//Professional, managerial and technical workers 
	$pmt_workers  = $l5_row[1]+$l5_row[2];
	//total workers
	$t_workers = $l5_row[3];
	$pmt_rate = $pmt_workers/$t_workers;
	
	$l5_score = (($pmt_rate*100)/$location_q5)*100;
	$l5_score = cap_score($l5_score);

	$weighted_score = calc_weighted_score($location_q5,$l5_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($location_scores, $l5_score);
	
	//*************************************************************************************************************
	//Extra location Questions
	//extra location 1 - Extra Question 1. An area with high speed broadband is important.
	if($location_extra == 1){
	$lx1_row = pg_fetch_row($location_extra_result_1);
	
	//total homes
	$num_all_homes = $lx1_row[5] - $lx1_row[4];
	//homes with broadband
	$num_selected_homes = $lx1_row[1];
	$home_percentage = 	($num_selected_homes/$num_all_homes)*100;
	
	//get score by dividing the % of homes with bband by the users input	
	$lx1_score = ($home_percentage/$location_extra_1)*100;
	$lx1_score = cap_score($lx1_score);
	
	$weighted_score = calc_weighted_score($location_extra_1,$lx1_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($location_scores, $lx1_score);
	
	//extra location 2 - Extra Question 2. An area with agriculture, manual and unskilled workers is preferable.
	$lx2_row = pg_fetch_row($location_extra_result_2);
	
	//total workers
	$num_all_workers = $lx2_row[7];
	//agriculture, manual and unskilled workers
	$num_selected_workers = $lx2_row[1] + $lx2_row[2] + $lx2_row[3] + $lx2_row[4] + $lx2_row[5];
	$worker_percentage = ($num_selected_workers/$num_all_workers)*100;
	
	$lx2_score = ($worker_percentage/$location_extra_2)*100;
	$lx2_score = cap_score($lx2_score);

	$weighted_score = calc_weighted_score($location_extra_2,$lx2_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($location_scores, $lx2_score);
	
	}
	
	//=============================================================================================================
	$healthcare_scores=array();

	//healthcare1 - Question 11. An area with a hospital nearby is important.

	$hc1_row = pg_fetch_row($healthcare_result_1);
	//user input converted to KM
	$hc1_q1 = $healthcare_q1*1000;
	$hospital_distance = $hc1_row[1];
	$hospital_distance = cap_value($hospital_distance,$hc1_q1);

	$hc1_score = ($hc1_q1 / $hospital_distance) * 100;
	$weighted_score = calc_weighted_score($healthcare_q1,$hc1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($healthcare_scores, $hc1_score);
	
	//healthcare2 - Question 12. An area with a doctor nearby is important.

	$hc2_row = pg_fetch_row($healthcare_result_2);
	//user input converted to KM
	$hc2_q2 = $healthcare_q2*1000;
	$doc_distance = $hc2_row[1];
	$doc_distance = cap_value($doc_distance,$hc2_q2);

	$hc2_score = ($hc2_q2 / $doc_distance) * 100;
	$weighted_score = calc_weighted_score($healthcare_q2,$hc2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($healthcare_scores, $hc2_score);
	
	//healthcare3 - Question 13. An area with a pharmacy nearby is important.
	
	$hc3_row = pg_fetch_row($healthcare_result_3);
	//user input converted to KM
	$hc3_q3 = $healthcare_q3*1000;
	$pharm_distance = $hc3_row[1];
	$pharm_distance = cap_value($pharm_distance,$hc3_q3);

	
	$hc3_score = ($hc3_q3 / $pharm_distance) * 100;
	$weighted_score = calc_weighted_score($healthcare_q3,$hc3_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($healthcare_scores, $hc3_score);
	
	//*************************************************************************************************************
	//Extra healthcare Questions
	//extra healthcare 1 - Extra Question 1. An area with a dentist nearby is important.

	if($healthcare_extra == 1){
	$hcx1_row = pg_fetch_row($healthcare_extra_result_1);
	//user input converted to KM
	$hcx1 = $healthcare_extra_1*1000;
	$dent_distance = $hcx1_row[1];
	$dent_distance = cap_value($dent_distance,$hcx1);

	$hcx1_score = ($hcx1/ $dent_distance) * 100;
	$weighted_score = calc_weighted_score($healthcare_extra_1,$hcx1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($healthcare_scores, $hcx1_score);

	//extra healthcare 2 - Extra Question 2. An area with a nursing home is important.

	$hcx2_row = pg_fetch_row($healthcare_extra_result_2);
	//user input converted to KM
	$hcx2 = $healthcare_extra_2*1000;
	$nursing_home_distance = $hcx2_row[1];
	$nursing_home_distance = cap_value($nursing_home_distance,$hcx2);

	$hcx2_score = ($hcx2/ $nursing_home_distance) * 100;
	$weighted_score = calc_weighted_score($healthcare_extra_2,$hcx2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($healthcare_scores, $hcx2_score);
	}
	
	//=============================================================================================================
	$education_scores=array();	
	//education 1 - Question 14. An area with primary and secondary schools nearby is important.
	
	$ed1_row = pg_fetch_row($education_result_1);
	$ed1_score = calc_exponential(1.13460089,1.034596995,$education_q1,$ed1_row[1]);
	$weighted_score = calc_weighted_score($education_q1,$ed1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($education_scores, $ed1_score);

	//education 2 - Question 15. An area nearby a college or University would be desirable.
	$ed2_row = pg_fetch_row($education_result_2);
	//user input converted to KM
	$ed2_q2 = $education_q2*1000;
	$uni_distance = $ed2_row[1];	
	$uni_distance = cap_value($uni_distance,$ed2_q2);
	
	$ed2_score = ($ed2_q2 / $uni_distance) * 100;
	$weighted_score = calc_weighted_score($education_q2,$ed2_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($education_scores, $ed2_score);
	
	//*************************************************************************************************************
	//Extra education Questions
	//extra education 1 - Extra Question 1. An area with pre-schools nearby is important.
	if($education_extra == 1){

	$edx1_row = pg_fetch_row($education_extra_result_1);
	$edx1_score = calc_exponential(0.9424524425,1.034957705,$education_extra_1,$edx1_row[1]);
	$weighted_score = calc_weighted_score($education_extra_1,$edx1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($education_scores, $edx1_score);
	
	//extra education 2 - Extra Question 2. An area with a library nearby would be desirable
	$edx2_row = pg_fetch_row($education_extra_result_2);
	$edx2_score = calc_exponential(0.8684973009,1.029649296,$education_extra_2,$edx2_row[1]);
	$weighted_score = calc_weighted_score($education_extra_2,$edx2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($education_scores, $edx2_score);
	}

	
	//=============================================================================================================
	
	//amenities 
	$amenities_scores=array();
	//amenities1 - Question 16. An area with leisure activities nearby is desirable.
	$am1_row = pg_fetch_row($amenities_result_1);
	$am1_score = calc_exponential(1.090100715,1.033023465,$amenities_q1,$am1_row[1]);
	$weighted_score = calc_weighted_score($amenities_q1,$am1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $am1_score);
	
	//amenities2 - Question 17. An area with sporting activities nearby is desirable.
	$am2_row = pg_fetch_row($amenities_result_2);
	$am2_score = calc_exponential(1.410219162,1.049896477,$amenities_q2,$am2_row[1]);
	$weighted_score = calc_weighted_score($amenities_q2,$am2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $am2_score);

	//amenities3 - Question 18. An area with bars / restaurants nearby is desirable.
	$am3_row = pg_fetch_row($amenities_result_3);
	$am3_score = calc_exponential(2.013653727,1.066059667,$amenities_q3,$am3_row[1]);
	$weighted_score = calc_weighted_score($amenities_q3,$am3_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $am3_score);
	
	//amenities4 - Question 19. An area with grocery stores / supermarkets nearby is desirable.
	$am4_row = pg_fetch_row($amenities_result_4);
	$am4_score = calc_exponential(1.523415379,1.062997211,$amenities_q4,$am4_row[1]);
	$weighted_score = calc_weighted_score($amenities_q4,$am4_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $am4_score);
	
	//amenities5 - Question 20. An area with Retail Stores nearby is desirable.
	$am5_row = pg_fetch_row($amenities_result_5);
	$am5_score = calc_exponential(1.467799268,1.056767382,$amenities_q5,$am5_row[1]);
	$weighted_score = calc_weighted_score($amenities_q5,$am5_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $am5_score);
	
	//amenities6 - Question 21. An area with a tourist attractions nearby is desirable.
	$am6_row = pg_fetch_row($amenities_result_6);
	$am6_score = calc_exponential(1.604567966,1.061060904,$amenities_q6,$am6_row[1]);
	$weighted_score = calc_weighted_score($amenities_q6,$am6_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $am6_score);
	
	//*************************************************************************************************************
	//Extra amenities Questions
	
	if($amenities_extra == 1){
	//extra amenities 1 - Extra Question 1. An area with a fire station garage nearby is desirable.

	$amx1_row = pg_fetch_row($amenities_extra_result_1);
	//convert to KM
	$amx1 = $amenities_extra_1*1000;
	$firestation_distance = $amx1_row[1];
	$firestation_distance = cap_value($firestation_distance,$amx1);

	$amx1_score = ($amx1 / $firestation_distance) * 100;
	$weighted_score = calc_weighted_score($amenities_extra_1,$amx1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $amx1_score);
	
	//extra amenities 2 - Extra Question 2. An area with a post office nearby is desirable.
	$amx2_row = pg_fetch_row($amenities_extra_result_2);
	//convert to KM
	$amx2 = $amenities_extra_2*1000;
	$postoffice_distance = $amx2_row[1];
	$postoffice_distance = cap_value($postoffice_distance,$amx2);

	$amx2_score = ($amx2 / $postoffice_distance) * 100;
	$weighted_score = calc_weighted_score($amenities_extra_2,$amx2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($amenities_scores, $amx2_score);
	
	}

	//=============================================================================================================

	//people
	$people_scores=array();
	//people1 - Question 22. An area with a high rate of Irish Speakers is important.

	$p1_row = pg_fetch_row($people_result_1);
	$irish_speakers = ($p1_row[1]/$p1_row[2])*100;
	if($irish_speakers > $people_q1){
		$irish_speakers = $people_q1;
	}
	$p1_score = ($irish_speakers / $people_q1) * 100;
	
	$weighted_score = calc_weighted_score($people_q1,$p1_score,$total_input);
	
	array_push($query_scores, $weighted_score);	
	array_push($people_scores, $p1_score);
		
	//people2 - Question 23. Is the type of people in the area important? Select preferred type of people in area.

	$p2_row = pg_fetch_row($people_result_2);
	$families = $p2_row[1]+$p2_row[3]+$p2_row[5]+$p2_row[7]+$p2_row[8];
	$students = $p2_row[2]+$p2_row[4];
	$retired = $p2_row[6];

	$total_people = $p2_row[9];
	$total_selected_people = ($families*$people_q2_a)+ ($students*$people_q2_b) + ($retired*$people_q2_c);
	
	$p2_score = ($total_selected_people / $total_people) * 100;

	$weighted_score = calc_weighted_score($people_q2,$p2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($people_scores, $p2_score);
	
	//people3 - Question 24. Is the cultural background of the area important? Select preferred cultural background of people in area

	$p3_row = pg_fetch_row($people_result_3);
	$irish_white = $p3_row[1];
	$irish_traveller = $p3_row[2];
	$other_white = $p3_row[3];
	$black = $p3_row[4];
	$asian = $p3_row[5];
	$other = $p3_row[6];

	$total_people = $p3_row[8] - $p3_row[7];
	$total_selected_people = ($irish_white*$people_q3_a) + ($irish_traveller*$people_q3_b) + ($other_white*$people_q3_c) + ($black*$people_q3_d) + ($asian*$people_q3_e) + ($other*$people_q3_f);
	
	$p3_score = ($total_selected_people / $total_people) * 100;
	
	$weighted_score = calc_weighted_score($people_q3,$p3_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($people_scores, $p3_score);
	
	//people4 - Question 25. Is the religion of the area important? Select the preferred religion of the area.

	$p4_row = pg_fetch_row($people_result_4);
	$catholic = $p4_row[1];
	$other = $p4_row[2];
	$none = $p4_row[3];

	$total_people = $p4_row[5]-$p4_row[4];
	$total_selected_people = ($catholic*$people_q4_a)+ ($other*$people_q4_b) + ($none*$people_q4_c);
	
	$p4_score = ($total_selected_people / $total_people) * 100;
	
	$weighted_score = calc_weighted_score($people_q4,$p4_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($people_scores, $p4_score);
	
	//*************************************************************************************************************
	//Extra people Questions
	//extra people 1 - Extra Question 1. Is the general health of the area important?

	if($people_extra == 1){
	$px1_row = pg_fetch_row($people_extra_result_1);
	
	//all people in the area
	$num_all_people = $px1_row[4] - $px1_row[3];
	//healthy people in the area
	$num_selected_people = $px1_row[1] + $px1_row[2];
	$person_percentage = ($num_selected_people/$num_all_people)*100;
	
	$px1_score = ($person_percentage/$people_extra_1)*100;
	if($px1_score > 100){
		$px1_score = 100;
	}

	$weighted_score = calc_weighted_score($people_extra_1,$px1_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($people_scores, $px1_score);
	}
		
	//=============================================================================================================
	
	$transport_scores=array();
	//Transport1 - Question 26. An area with a lower average commute time is preferable.
	$t1_row = pg_fetch_row($transport_result_1);
	$num_lengths_of_commute = 0;
	if($transport_q1 <= 100){
	$num_lengths_of_commute += $t1_row[1];
	}if($transport_q1 < 85){
	$num_lengths_of_commute += $t1_row[2];
	}if($transport_q1 < 70){
	$num_lengths_of_commute += $t1_row[3];
	}if($transport_q1 < 50){
	$num_lengths_of_commute += $t1_row[4];
	}if($transport_q1 < 30){
	$num_lengths_of_commute += $t1_row[5];
	}if($transport_q1 < 15){
	$num_lengths_of_commute += $t1_row[6];
	}	
	
	$total_people = $t1_row[8]-$t1_row[7];

	$t1_score = ($num_lengths_of_commute / $total_people) * 100;
	
	$weighted_score = calc_weighted_score($transport_q1,$t1_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($transport_scores, $t1_score);

	//Transport2 - Question 27. High public transport usage in the area is desirable.
	$t2_row = pg_fetch_row($transport_result_2);
	$total_public_transport_users = $t2_row[1] + $t2_row[2];
	$total_people = $t2_row[3];
	
	$transport_users = $total_public_transport_users/$total_people;
	$transport_users = $transport_users*100;
	
	if($transport_users > $transport_q2){
		$transport_users = $transport_q2;
	}
	
	$t2_score = ($transport_users/$transport_q2)*100;	
	
	$weighted_score = calc_weighted_score($transport_q2,$t2_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($transport_scores, $t2_score);

	//transport 3 - Question 28. A high number of public transport stops nearby.
	$t3_row = pg_fetch_row($transport_result_3);
	$t3_score = calc_exponential(1.268522359,1.051382908,$transport_q3,$t3_row[1]);
	$weighted_score = calc_weighted_score($transport_q3,$t3_score,$total_input);
	
	array_push($query_scores, $weighted_score);
	array_push($transport_scores, $t3_score);
	
	
	//	
	//*************************************************************************************************************
	//Extra transport Questions
	if($transport_extra == 1){
	$tx1_row = pg_fetch_row($transport_extra_result_1);

	$num_all_car = $tx1_row[7] - $tx1_row[6];
	$num_selected_car = $tx1_row[2] + $tx1_row[3] +  $tx1_row[4] +  $tx1_row[5];
	$car_percentage = ($num_selected_car/$num_all_car)*100;
	
	$tx1_score = ($car_percentage/$transport_extra_1)*100;
	$tx1_score = cap_score($tx1_score);
	
	$weighted_score = calc_weighted_score($transport_extra_1,$tx1_score,$total_input);
	array_push($query_scores, $weighted_score);
	array_push($transport_scores, $tx1_score);
	}
	
	
	//=============================================================================================================
	
	//individual unweighted scores for each section
	$housing_scores_total = round( array_sum($housing_scores) / count($housing_scores));
	$location_scores_total = round( array_sum($location_scores) / count($location_scores));
	$healthcare_scores_total = round( array_sum($healthcare_scores) / count($healthcare_scores));
	$education_scores_total = round( array_sum($education_scores) / count($education_scores));
	$amenities_scores_total = round( array_sum($amenities_scores) / count($amenities_scores));
	$people_scores_total = round( array_sum($people_scores) / count($people_scores));
	$transport_scores_total = round( array_sum($transport_scores) / count($transport_scores));
	
	//overall score
	$score = array_sum($query_scores);

	//=============================================================================================================
	
	$rounded_score = round($score);
	
	//strings added to the geojson array to construct the geojson file for the output
	if($count < $num_lines) { 
		array_push($geoJSON_array,"{ \"type\": \"Feature\", \"properties\": { \"gid\": $row[0],  \"score\": $rounded_score , \"countyname\": \"$row[1]\" , \"edname\": \"$row[3]\", \"housing_score\": \"$housing_scores_total\",\"location_score\": \"$location_scores_total\",\"healthcare_score\": \"$healthcare_scores_total\",\"education_score\": \"$education_scores_total\",\"amenities_score\": \"$amenities_scores_total\",\"people_score\": \"$people_scores_total\",\"transport_score\": \"$transport_scores_total\" }, \"geometry\": $row[2]},\n");
	}
	else{
		array_push($geoJSON_array,"{ \"type\": \"Feature\", \"properties\": { \"gid\": $row[0],  \"score\": $rounded_score , \"countyname\": \"$row[1]\" , \"edname\": \"$row[3]\" , \"housing_score\": \"$housing_scores_total\",\"location_score\": \"$location_scores_total\",\"healthcare_score\": \"$healthcare_scores_total\",\"education_score\": \"$education_scores_total\",\"amenities_score\": \"$amenities_scores_total\",\"people_score\": \"$people_scores_total\",\"transport_score\": \"$transport_scores_total\" }, \"geometry\": $row[2]}\n");
	}

//adds score to the sorting array
$sort_scores[$row[0]] = $score;

}
//adds the final piece to the geojson array 
array_push($geoJSON_array,"]}");

//sorts the scores
arsort($sort_scores);

$top_scores=array();

//creates the top 15 scores
foreach($sort_scores as $x => $x_value) {
    $count++;
	if($count<=15){
		array_push($top_scores, "$x");
	}
	if($count==15){
		break;
	}

}

//combines all the strings in the geojson array to be 1 string
$out = implode($geoJSON_array);

//calculates the weighted score for the given question
function calc_weighted_score($input_value, $q_score,$total_input) {
   $input_weight = $input_value/$total_input;
   $weighted_score = $input_weight * $q_score;
   return $weighted_score;
}

//invert the input from the slider for distances 
function invert_distance_score($distance_question_input) {
	$max_value = 100;
	$inverted = $max_value-$distance_question_input;
	if($inverted == 0){
		$inverted = 1;
	}
	return $inverted; 
}

//function to adjust the slider value for non agree disagree format questions
function calc_weighting_non_agree_disagree($slider_value) {
	$middle_value = 50;
	//as the value is calulated from the distance from the middle of the slider
	//the score is multipled by 2 to make it equivalent to other slider values
	$adjusted_slider_value = abs($middle_value-$slider_value)*2;
	return $adjusted_slider_value;
}

//function to calculate score for all questions using this method
function calc_exponential($A,$B,$slider_value,$area_value){
	$b_pow = pow ($B, $slider_value);
	$exp =  $A * ($b_pow);
	
	//calculates score as a percentage value
	$score = ($area_value/$exp)*100;
	return cap_score($score);
}

//caps score at 100;
function cap_score($score){
	if($score > 100){
		$score = 100;
	}
	return $score;
}

//caps a value at another given value
function cap_value($x,$y){
	if($x < $y){
		$x = $y;
	}
	return $x;
}

?>
<!DOCTYPE html>
<html>
<head>
	
	<title>Search Results</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />
 
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/extra.css" rel="stylesheet">
    <link href="leaflet/extra_leaflet.css" rel="stylesheet">


	<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
	<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

		
</head>
<body>

    <nav id="siteNav" class="navbar" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">
                	Spatial Property Search
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active">
                        <a href="home.html">Home</a>
                    </li>
                    <li>
                        <a href="home.html#about">About</a>
                    </li>
                </ul>
                
            </div>
        </div>
    </nav>

<div id='map'></div>
<div>
<button id="close-image" onclick="document.getElementById('places').scrollIntoView();"><img src="https://image.flaticon.com/icons/svg/53/53604.svg"></button>
</div>

<div class="container" id="places">
  <h2>Top 15 areas that are recommended for you!</h2>
  <ul class="list-group">
    <li class="list-group-item"><h1 id="list00">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 0); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list01">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 1); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list02">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 2); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list03">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 3); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list04">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 4); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list05">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 5); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list06">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 6); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list07">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 7); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list08">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 8); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list09">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 9); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list10">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 10); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list11">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 11); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list12">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 12); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list13">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 13); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list14">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 14); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    </ul>
</div>

<script type="text/javascript">
//loads the GeoJSON file from the PHP
var smallAreas= <?php echo ($out); ?>;
</script>

<script type="text/javascript">
//loads the top 15 list from the PHP
var top15List= <?php echo json_encode($top_scores); ?>;
</script>

<script type="text/javascript" src="leaflet/extra_leaflet.js"></script>

<script type="text/javascript">

//function to load the values into the top 15 list
window.onload = function() {
	var list = ["list00","list01","list02","list03","list04","list05","list06","list07","list08","list09","list10","list11","list12","list13","list14"];
	for(var i =0; i<list.length; i++){
		area_ID = parseInt(top15List[i]);
		geojson.eachLayer(function(layer) {
  		if (layer.feature.properties.layerID === area_ID) {
        	var element = document.getElementById(list[i]);
			element.innerHTML = layer.feature.properties.edname + ", " + layer.feature.properties.countyname + "<br> Score " + layer.feature.properties.score;
    	}
    });
  }
}

</script>

</body>
</html>
