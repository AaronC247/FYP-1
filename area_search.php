<?php
$db = pg_connect("host=localhost port=5432 dbname=Maptest user=postgres password=");

//cords of inputted values
$long = $_POST['longitude'];
$lat = $_POST['latitude'];

//creates query to find area that contains the location of inputted coords
$get_location_query ="SELECT gid FROM census WHERE ST_Contains(geom, ST_GeometryFromText('POINT($long $lat)', 29903));";
$get_location_result = pg_query($get_location_query); 

$location_gid = -1;

//loads the result of the query into the location_gid variable
while($location_row = pg_fetch_row($get_location_result)){
	$location_gid = $location_row[0];
}

//if the location is not found within an area
//the user is returned to the search page
if($location_gid == -1){
	header('Location: compare.html#false'); 
}

//otherwise we begin the processing
else{

$geoJSON_array=array();
$sort_scores=array();

//Housing Querys
$housing_query_1 ="select gid, T6_1_HB_H, T6_1_FA_H, T6_1_BS_H, T6_1_CM_H, T6_1_NS_H, T6_1_TH from census_2016_data order by gid;";
$housing_query_1_fixed_gid = "select gid, T6_1_HB_H, T6_1_FA_H, T6_1_BS_H, T6_1_CM_H, T6_1_NS_H, T6_1_TH from census_2016_data where gid = $location_gid;";
$housing_result_1 = pg_query($housing_query_1); 
$housing_result_1_fixed_gid = pg_query($housing_query_1_fixed_gid); 

$housing_query_2_fixed_gid = "SELECT gid,T6_2_PRE19H,T6_2_19_45H,T6_2_46_60H,T6_2_61_70H,T6_2_71_80H,T6_2_81_90H,T6_2_91_00H,T6_2_01_10H,T6_2_11LH,T6_2_NSH,T6_2_TH FROM census_2016_data where gid = $location_gid;";
$housing_query_2 = "SELECT gid,T6_2_PRE19H,T6_2_19_45H,T6_2_46_60H,T6_2_61_70H,T6_2_71_80H,T6_2_81_90H,T6_2_91_00H,T6_2_01_10H,T6_2_11LH,T6_2_NSH,T6_2_TH FROM census_2016_data order by gid;"; 
$housing_result_2 = pg_query($housing_query_2); 
$housing_result_2_fixed_gid = pg_query($housing_query_2_fixed_gid); 

$housing_query_3_fixed_gid = "Select gid, T6_4_1RH, T6_4_2RH, T6_4_3RH, T6_4_4RH, T6_4_5RH, T6_4_6RH, T6_4_7RH, T6_4_GE8RH, T6_4_NSH, T6_4_TH from census_2016_data where gid = $location_gid;";
$housing_query_3 ="Select gid, T6_4_1RH, T6_4_2RH, T6_4_3RH, T6_4_4RH, T6_4_5RH, T6_4_6RH, T6_4_7RH, T6_4_GE8RH, T6_4_NSH, T6_4_TH from census_2016_data order by gid;";
$housing_result_3 = pg_query($housing_query_3); 
$housing_result_3_fixed_gid = pg_query($housing_query_3_fixed_gid); 

$housing_query_4_fixed_gid = "select gid, T6_3_OMLH, T6_3_OOH, T6_3_RPLH, T6_3_RLAH, T6_3_RVCHBH, T6_3_OFRH, T6_3_NSH, T6_3_TH from census_2016_data where gid = $location_gid;";
$housing_query_4 ="select gid, T6_3_OMLH, T6_3_OOH, T6_3_RPLH, T6_3_RLAH, T6_3_RVCHBH, T6_3_OFRH, T6_3_NSH, T6_3_TH from census_2016_data order by gid;";
$housing_result_4 = pg_query($housing_query_4); 
$housing_result_4_fixed_gid = pg_query($housing_query_4_fixed_gid); 

$housing_query_5_fixed_gid = "select gid, avg_price from house_prices_gid where gid = $location_gid;";
$housing_query_5 ="select gid, avg_price from house_prices_gid order by gid;";
$housing_result_5 = pg_query($housing_query_5);
$housing_result_5_fixed_gid = pg_query($housing_query_5_fixed_gid); 

$housing_extra_query_1_fixed_gid = "select gid, T6_5_NCH,T6_5_OCH,T6_5_NGCH,T6_5_ECH,T6_5_CCH,T6_5_PCH,T6_5_LPGCH,T6_5_WCH,T6_5_OTH,T6_5_NS,T6_5_T from census_2016_data where gid = $location_gid;";
$housing_extra_query_1 ="select gid, T6_5_NCH,T6_5_OCH,T6_5_NGCH,T6_5_ECH,T6_5_CCH,T6_5_PCH,T6_5_LPGCH,T6_5_WCH,T6_5_OTH,T6_5_NS,T6_5_T from census_2016_data order by gid;";
$housing_extra_result_1 = pg_query($housing_extra_query_1);
$housing_extra_result_1_fixed_gid = pg_query($housing_extra_query_1_fixed_gid);

$housing_extra_query_2_fixed_gid = "select gid, T6_6_PM,T6_6_GSLA,T6_6_GSP,T6_6_OP,T6_6_N,T6_6_NS,T6_6_T,T6_7_PS from census_2016_data where gid = $location_gid;";
$housing_extra_query_2 ="select gid, T6_6_PM,T6_6_GSLA,T6_6_GSP,T6_6_OP,T6_6_N,T6_6_NS,T6_6_T,T6_7_PS from census_2016_data order by gid;";
$housing_extra_result_2 = pg_query($housing_extra_query_2);
$housing_extra_result_2_fixed_gid = pg_query($housing_extra_query_2_fixed_gid);

//Location Querys
$location_q1 = "select a.gid, a.T1_1AGETT, b.shape__are from census_2016_data a, census b where a.gid = $location_gid and a.gid = b.gid";
$location_query_1 ="select a.gid, a.T1_1AGETT, b.shape__are from census_2016_data a, census b where a.gid = b.gid order by a.gid;";
$location_result_1 = pg_query($location_query_1);
$location_result_1a = pg_query($location_q1);

$location_q2 = "SELECT census_gid,total2011,atmahro,dnao,ko,reho,bro,tro,fdo,cdo,weo,dpe,pooso,gjoo from crime_area_avgs where census_gid = $location_gid;";
$location_query_2 ="SELECT census_gid,total2011,atmahro,dnao,ko,reho,bro,tro,fdo,cdo,weo,dpe,pooso,gjoo from crime_area_avgs order by census_gid";
$location_result_2 = pg_query($location_query_2); 
$location_result_2a = pg_query($location_q2); 

$location_q3 = "SELECT  gid, T9_1_TT, T8_1_ULGUPJT  FROM census_2016_data where gid = $location_gid";
$location_query_3 ="SELECT  gid, T9_1_TT, T8_1_ULGUPJT  FROM census_2016_data order by gid;";
$location_result_3 = pg_query($location_query_3); 
$location_result_3a = pg_query($location_q3); 

$location_q4 = "Select gid, T10_4_ODNDT, T10_4_HDPQT, T10_4_PDT, T10_4_DT, T10_4_NST, T10_4_TT from census_2016_data where gid = $location_gid;";
$location_query_4 ="Select gid, T10_4_ODNDT, T10_4_HDPQT, T10_4_PDT, T10_4_DT, T10_4_NST, T10_4_TT from census_2016_data order by gid;";
$location_result_4 = pg_query($location_query_4); 
$location_result_4a = pg_query($location_q4); 

$location_q5 = "Select gid, T9_1_PWT, T9_1_MTT, T9_1_TT from census_2016_data where gid = $location_gid;";
$location_query_5 ="Select gid, T9_1_PWT, T9_1_MTT, T9_1_TT from census_2016_data order by gid;";
$location_result_5 = pg_query($location_query_5);
$location_result_5a = pg_query($location_q5);

$location_extra_1 = "select gid, T15_3_B,T15_3_OTH,T15_3_N,T15_3_NS,T15_3_T from census_2016_data where gid = $location_gid;";
$location_extra_query_1 ="select gid, T15_3_B,T15_3_OTH,T15_3_N,T15_3_NS,T15_3_T from census_2016_data order by gid;";
$location_extra_result_1 = pg_query($location_extra_query_1);
$location_extra_result_1a = pg_query($location_extra_1);

$location_extra_2 = "Select gid, T9_2_PI, T9_2_PJ,T9_2_PE,T9_2_PF,T9_2_PG,T9_2_PZ,T9_2_PT from census_2016_data where gid = $location_gid;";
$location_extra_query_2 ="Select gid, T9_2_PI, T9_2_PJ,T9_2_PE,T9_2_PF,T9_2_PG,T9_2_PZ,T9_2_PT from census_2016_data order by gid;";
$location_extra_result_2 = pg_query($location_extra_query_2);
$location_extra_result_2a = pg_query($location_extra_2);


//Healthcare Querys
$healthcare_q1 = "SELECT gid, distance FROM public.hos_dis where gid = $location_gid;";
$healthcare_query_1 ="SELECT gid, distance FROM public.hos_dis Order By gid";
$healthcare_result_1 = pg_query($healthcare_query_1); 
$healthcare_result_1a = pg_query($healthcare_q1); 

$healthcare_q2 = "SELECT gid, distance FROM public.doc_dist where gid = $location_gid;";
$healthcare_query_2 ="SELECT gid, distance FROM public.doc_dist Order By gid";
$healthcare_result_2 = pg_query($healthcare_query_2); 
$healthcare_result_2a = pg_query($healthcare_q2); 

$healthcare_q3 = "SELECT gid, distance FROM public.pharm_dist where gid = $location_gid;";
$healthcare_query_3 ="SELECT gid, distance FROM public.pharm_dist Order By gid";
$healthcare_result_3 = pg_query($healthcare_query_3); 
$healthcare_result_3a = pg_query($healthcare_q3); 

$healthcare_extra_1 = "SELECT gid, distance FROM public.dent_dist where gid = $location_gid";
$healthcare_extra_query_1 ="SELECT gid, distance FROM public.dent_dist Order By gid";
$healthcare_extra_result_1 = pg_query($healthcare_extra_query_1);
$healthcare_extra_result_1a = pg_query($healthcare_extra_1);

$healthcare_extra_2 = "SELECT gid, distance FROM public.nursing_home_dist where gid = $location_gid";
$healthcare_extra_query_2 = "SELECT gid, distance FROM public.nursing_home_dist Order By gid";
$healthcare_extra_result_2 = pg_query($healthcare_extra_query_2);
$healthcare_extra_result_2a = pg_query($healthcare_extra_2);


//Education Querys
$education_q1 = "Select gid, count from public.schools_count where gid = $location_gid;";
$education_query_1 ="Select gid, count from public.schools_count order by gid;";
$education_result_1 = pg_query($education_query_1); 
$education_result_1a = pg_query($education_q1); 

$education_q2 = "SELECT gid, distance FROM public.uni_dist where gid = $location_gid;";
$education_query_2 ="SELECT gid, distance FROM public.uni_dist Order By gid";
$education_result_2 = pg_query($education_query_2); 
$education_result_2a = pg_query($education_q2); 

$education_extra_1 = "Select gid, count from preschool_count where gid = $location_gid";
$education_extra_query_1 ="Select gid, count from preschool_count order by gid;";
$education_extra_result_1 = pg_query($education_extra_query_1);
$education_extra_result_1a = pg_query($education_extra_1);

$education_extra_2 ="Select gid, count from library_count where gid = $location_gid;";
$education_extra_query_2 ="Select gid, count from library_count order by gid;";
$education_extra_result_2 = pg_query($education_extra_query_2);
$education_extra_result_2a = pg_query($education_extra_2);

//amenities Querys 
$amenities_q1 = "Select gid, count from lesiure_table where gid = $location_gid;";
$amenities_query_1 ="Select gid, count from lesiure_table order by gid;";
$amenities_result_1 = pg_query($amenities_query_1); 
$amenities_result_1a = pg_query($amenities_q1); 

$amenities_q2 = "Select gid, count from activity_table where gid = $location_gid;";
$amenities_query_2 ="Select gid, count from activity_table order by gid;";
$amenities_result_2 = pg_query($amenities_query_2); 
$amenities_result_2a = pg_query($amenities_q2); 

$amenities_q3 = "Select gid, count from rest_and_bars_count where gid = $location_gid;";
$amenities_query_3 ="Select gid, count from rest_and_bars_count order by gid;";
$amenities_result_3 = pg_query($amenities_query_3); 
$amenities_result_3a = pg_query($amenities_q3); 

$amenities_q4 = "Select gid, count from groce_stores_count where gid = $location_gid;";
$amenities_query_4 ="Select gid, count from groce_stores_count order by gid;";
$amenities_result_4 = pg_query($amenities_query_4); 
$amenities_result_4a = pg_query($amenities_q4); 

$amenities_q5 = "Select gid, count from retail_stores_count where gid = $location_gid;";
$amenities_query_5 ="Select gid, count from retail_stores_count order by gid;";
$amenities_result_5 = pg_query($amenities_query_5); 
$amenities_result_5a = pg_query($amenities_q5); 

$amenities_q6 = "Select gid, count from tourism_count where gid = $location_gid;";
$amenities_query_6 ="Select gid, count from tourism_count order by gid;";
$amenities_result_6 = pg_query($amenities_query_6); 
$amenities_result_6a = pg_query($amenities_q6); 

$amenities_extra_1 ="Select gid, distance from fire_station_dist where gid = $location_gid;";
$amenities_extra_query_1 ="Select gid, distance from fire_station_dist order by gid;";
$amenities_extra_result_1 = pg_query($amenities_extra_query_1);
$amenities_extra_result_1a = pg_query($amenities_extra_1);

$amenities_extra_2 ="Select gid, distance from post_office_dist where gid = $location_gid;";
$amenities_extra_query_2 ="Select gid, distance from post_office_dist order by gid;";
$amenities_extra_result_2 = pg_query($amenities_extra_query_2);
$amenities_extra_result_2a = pg_query($amenities_extra_2);


//People Querys
$people_q1 = "SELECT gid, T3_2ALLT, T1_2T FROM census_2016_data where gid = $location_gid;";
$people_query_1 ="SELECT gid, T3_2ALLT, T1_2T FROM census_2016_data order by gid";
$people_result_1 = pg_query($people_query_1); 
$people_result_1a = pg_query($people_q1); 

$people_q2 = "Select gid, T8_1_WT, T8_1_LFFJT, T8_1_ULGUPJT, T8_1_ST, T8_1_LAHFT, T8_1_RT, T8_1_UTWSDT, T8_1_OTHT, T8_1_TT from census_2016_data where gid = $location_gid";
$people_query_2 ="Select gid, T8_1_WT, T8_1_LFFJT, T8_1_ULGUPJT, T8_1_ST, T8_1_LAHFT, T8_1_RT, T8_1_UTWSDT, T8_1_OTHT, T8_1_TT from census_2016_data order by gid;";
$people_result_2 = pg_query($people_query_2); 
$people_result_2a = pg_query($people_q2); 

$people_q3 = "Select gid, T2_2WI,T2_2WIT,T2_2OW,T2_2BBI,T2_2AAI,T2_2OTH,T2_2NS,T2_2T FROM census_2016_data where gid = $location_gid";
$people_query_3 ="Select gid, T2_2WI,T2_2WIT,T2_2OW,T2_2BBI,T2_2AAI,T2_2OTH,T2_2NS,T2_2T from census_2016_data order by gid;";
$people_result_3 = pg_query($people_query_3);
$people_result_3a = pg_query($people_q3);

$people_q4 = "Select gid, T2_4CA,T2_4OR,T2_4NR,T2_4NS,T2_4T from census_2016_data where gid = $location_gid;";
$people_query_4 ="Select gid, T2_4CA,T2_4OR,T2_4NR,T2_4NS,T2_4T from census_2016_data order by gid;";
$people_result_4 = pg_query($people_query_4); 
$people_result_4a = pg_query($people_q4); 

$people_extra_1 = "Select gid, T12_3_VGT,T12_3_GT,T12_3_NST,T12_3_TT from census_2016_data where gid = $location_gid;";
$people_extra_query_1 ="Select gid, T12_3_VGT,T12_3_GT,T12_3_NST,T12_3_TT from census_2016_data order by gid;";
$people_extra_result_1 = pg_query($people_extra_query_1);
$people_extra_result_1a = pg_query($people_extra_1);

//transport querys
$transport_q1 = "SELECT gid, T11_3_D1,T11_3_D2,T11_3_D3,T11_3_D4,T11_3_D5,T11_3_D6,T11_3_NS,T11_3_T FROM census_2016_data where gid = $location_gid;";
$transport_query_1 ="SELECT gid, T11_3_D1,T11_3_D2,T11_3_D3,T11_3_D4,T11_3_D5,T11_3_D6,T11_3_NS,T11_3_T FROM census_2016_data order by gid";
$transport_result_1 = pg_query($transport_query_1); 
$transport_result_1a = pg_query($transport_q1); 

$transport_q2 = "SELECT gid, T11_1_BUT, T11_1_TDLT, T11_1_TT FROM census_2016_data where gid = $location_gid";
$transport_query_2 ="SELECT gid, T11_1_BUT, T11_1_TDLT, T11_1_TT FROM census_2016_data order by gid";
$transport_result_2 = pg_query($transport_query_2); 
$transport_result_2a = pg_query($transport_q2); 

$transport_q3 = "Select gid, count from trans_count where gid = $location_gid;";
$transport_query_3 ="Select gid, count from trans_count order by gid;";
$transport_result_3 = pg_query($transport_query_3); 
$transport_result_3a = pg_query($transport_q3); 

$transport_extra_1 ="SELECT gid,T15_1_NC,T15_1_1C,T15_1_2C,T15_1_3C,T15_1_GE4C,T15_1_NSC,T15_1_TC FROM census_2016_data where gid = $location_gid";
$transport_extra_query_1 ="SELECT gid,T15_1_NC,T15_1_1C,T15_1_2C,T15_1_3C,T15_1_GE4C,T15_1_NSC,T15_1_TC FROM census_2016_data order by gid";
$transport_extra_result_1 = pg_query($transport_extra_query_1);	
$transport_extra_result_1a = pg_query($transport_extra_1);	



array_push($geoJSON_array,"{'type': 'FeatureCollection','crs': { 'type': 'name', 'properties': { 'name': 'urn:ogc:def:crs:OGC:1.3:CRS84' } },'features':[");

$query ="SELECT gid, countyname, geometry, edname FROM public.census Order By gid";
$result = pg_query($query); 
$num_lines = pg_numrows($result);
$count = 0;

//caps scores at 0
function cap_similarity($value) {
	if($value < 0){
		$value = 0;
	}
	return $value;
}

function cap_similarity_1($value) {
	if($value > 1){
		$value = 1;
	}
	return $value;
}


//while loop to generate output file
while ($row = pg_fetch_row($result)) {
//	$row = pg_fetch_row($result);
	
	$query_scores=array();

	///start here /////
	$housing_scores=array();

	//house1 - comparing the types of homes
	$h1_row = pg_fetch_row($housing_result_1);
	$h1_row_selected_area = pg_fetch_row($housing_result_1_fixed_gid,0);
	
	$num_type_of_select_homes = 0;
	
	//get values for chosen location
	$apt_selected_area = $h1_row_selected_area[2]+$h1_row_selected_area[3];
	$home_selected_area = $h1_row_selected_area[1];
	$num_all_homes_selected_area = $h1_row_selected_area[6] - $h1_row_selected_area[5]  - $h1_row_selected_area[4];
	
	//get values for changing location
	$apt = $h1_row[2]+$h1_row[3];
	$home = $h1_row[1];
	$num_all_homes = $h1_row[6] - $h1_row[5]  - $h1_row[4];
	
	$pc_selected_areapt_selected_area = $apt_selected_area/$num_all_homes_selected_area;
	$pchome_selected_area = $home_selected_area/$num_all_homes_selected_area;
	$pc_selected_areapt = $apt/$num_all_homes;
	$pchome = $home/$num_all_homes;
	
	$thome = abs($pchome_selected_area - $pchome);
	$tapt = abs($pc_selected_areapt_selected_area - $pc_selected_areapt);
	//similarity between the two areas
	$similarity_h1 = 100-(($thome+$tapt)/2)*100;
	array_push($query_scores, $similarity_h1);
	array_push($housing_scores, $similarity_h1);	
	
	//house2 - comparing the age of homes
	$h2_row_selected_area = pg_fetch_row($housing_result_2_fixed_gid,0);
	$h2_row = pg_fetch_row($housing_result_2);
	
	$num_all_homes = $h2_row[11] - $h2_row[10];
	$num_all_homes_selected_area = $h2_row_selected_area[11] - $h2_row_selected_area[10];
	
	//difference
	$h2diff = 0;
	//total difference
	$th2diff=0;
		
	for ($x = 1; $x < 10; $x++) {
		$pc_selected_area = $h2_row_selected_area[$x]/$num_all_homes_selected_area;
		$pc = $h2_row[$x]/$num_all_homes;		
		$h2diff = ABS($pc_selected_area-$pc);
		$th2diff += $h2diff;
		
	}
	$similarity_h2 = ABS(1-$th2diff)*100;
	array_push($query_scores, $similarity_h2);
	array_push($housing_scores, $similarity_h2);	


	//house3 - compare size of home
	$h3_row_selected_area = pg_fetch_row($housing_result_3_fixed_gid,0);
	$h3_row = pg_fetch_row($housing_result_3);
	
	$num_all_homes = $h3_row[10] - $h3_row[9];
	$num_all_homes_selected_area = $h3_row_selected_area[10] - $h3_row_selected_area[9];
	
	$h3diff = 0;
	$th3diff=0;
		
	for ($x = 1; $x < 9; $x++) {
		$pc_selected_area = $h3_row_selected_area[$x]/$num_all_homes_selected_area;
		$pc = $h3_row[$x]/$num_all_homes;		
		$h3diff = ABS($pc_selected_area-$pc);
		$th3diff += $h3diff;
		
	}
	$similarity_h3 = ABS(1-$th3diff)*100;
	array_push($query_scores, $similarity_h3);
	array_push($housing_scores, $similarity_h3);	

		
	//house 4 - compare the type of ownership of homes
	$h4_row = pg_fetch_row($housing_result_4);
	$h4_row_selected_area = pg_fetch_row($housing_result_4_fixed_gid,0);
	
	$num_type_of_select_homes = 0;
	
	$rented = $h4_row[3]+$h4_row[4]+$h4_row[5];
	$owned = $h4_row[1]+$h4_row[2];
	$num_all_homes = $h4_row[8] - $h4_row[7]  - $h4_row[6];
	
	$rented_selected_area = $h4_row_selected_area[3]+$h4_row_selected_area[4]+$h4_row_selected_area[5];
	$owned_selected_area = $h4_row_selected_area[1]+$h4_row_selected_area[2];
	$num_all_homes_selected_area = $h4_row_selected_area[8] - $h4_row_selected_area[7]  - $h4_row_selected_area[6];
	
	$pcrented_selected_area = $rented_selected_area/$num_all_homes_selected_area;
	$pcowned_selected_area = $owned_selected_area/$num_all_homes_selected_area;
	$pcrented = $rented/$num_all_homes;
	$pcowned = $owned/$num_all_homes;

	$trented = abs($pcrented_selected_area - $pcrented);
	$towned = abs($pcowned_selected_area - $pcowned);
	
	$similarity_h4 = 100-(($trented+$towned)/2)*100;
	array_push($query_scores, $similarity_h4);
	array_push($housing_scores, $similarity_h4);	

	//house5	
	$h5_row = pg_fetch_row($housing_result_5);
	$h5_row_selected_area = pg_fetch_row($housing_result_5_fixed_gid,0);
	
	$selected_area_price = $h5_row_selected_area[1];
	$compared_area_price = $h5_row[1];
	
	$diff_h5 = abs($selected_area_price-$compared_area_price);
	$similarity_h5 = 100-(($diff_h5/$selected_area_price)*100);
	$similarity_h5 = cap_similarity($similarity_h5);
	array_push($query_scores, $similarity_h5);
	array_push($housing_scores, $similarity_h5);	
	
	//extra housing 1 - compare heating types
	$hx1_row = pg_fetch_row($housing_extra_result_1);
	$hx1_row_selected_area = pg_fetch_row($housing_extra_result_1_fixed_gid,0);
	
	$num_all_homes = $hx1_row[11] - $hx1_row[10];
	$num_selected_homes = $hx1_row[4] + $hx1_row[3];
	$compare_home_percentage = 	($num_selected_homes/$num_all_homes)*100;
	
	$num_all_homes = $hx1_row_selected_area[11] - $hx1_row_selected_area[10];
	$num_selected_homes = $hx1_row_selected_area[4] + $hx1_row_selected_area[3];
	$selected_home_percentage = ($num_selected_homes/$num_all_homes)*100;
	
	$diff_hx1 = abs($selected_home_percentage-$compare_home_percentage);
	if($selected_home_percentage ==0){
	$similarity_hx1 = 100-$diff_hx1;
	}else{
	$similarity_hx1 = 100-(($diff_hx1/$selected_home_percentage)*100);
	}
	$similarity_hx1 = cap_similarity($similarity_hx1);
	array_push($query_scores, $similarity_hx1);	
	array_push($housing_scores, $similarity_hx1);
	
	//extra housing 2 - compare water type
	
	$hx2_row = pg_fetch_row($housing_extra_result_2);
	$hx2_row_selected_area = pg_fetch_row($housing_extra_result_2_fixed_gid,0);

	$num_all_homes = $hx2_row[7] - $hx2_row[6];
	$num_selected_homes = $hx2_row[1] + $hx2_row[2];
	$compare_home_percentage = 	($num_selected_homes/$num_all_homes)*100;
	
	$num_all_homes = $hx2_row_selected_area[7] - $hx2_row_selected_area[6];
	$num_selected_homes = $hx2_row_selected_area[1] + $hx2_row_selected_area[2];
	$selected_home_percentage = ($num_selected_homes/$num_all_homes)*100;
	
	$diff_hx2 = abs($selected_home_percentage-$compare_home_percentage);
	if($selected_home_percentage ==0){
	$similarity_hx2 = 100-$diff_hx2;
	}else{
	$similarity_hx2 = 100-(($diff_hx2/$selected_home_percentage)*100);
	}
	
	$similarity_hx2 = cap_similarity($similarity_hx2);

	array_push($query_scores, $similarity_hx2);
	array_push($housing_scores, $similarity_hx2);	

	//=============================================================================================================
	//location1 - compare pop density
	$location_scores=array();
	
	$l1_row = pg_fetch_row($location_result_1);
	$l1_row_selected_area = pg_fetch_row($location_result_1a,0);
	
	$population = $l1_row[1];
	$area = $l1_row[2]*1000;
	$pop_density = $population/$area;
	
	$population_selected_area = $l1_row_selected_area[1];
	$area_selected_area = $l1_row_selected_area[2]*1000;
	$pop_density_selected_area = $population_selected_area/$area_selected_area;
	
	$diff_l1 = abs($pop_density_selected_area - $pop_density);

	$similarity_l1 = ($diff_l1/$pop_density_selected_area);
	$similarity_l1 = cap_similarity_1($similarity_l1);
	$similarity_l1 *= 100;
	$similarity_l1 = 100-$similarity_l1;
	
	array_push($query_scores, $similarity_l1);
	array_push($location_scores, $similarity_l1);

	//location2 -compare crime
	//Crime Query
	$l2_row = pg_fetch_row($location_result_2);
	$compared_total_crime = 0;
	$compared_total_pop = $l2_row[1];
	for($i = 2; $i<14; $i++){
		$compared_total_crime += $l2_row[$i];
	}
	$compared_crime_rate = ($compared_total_crime / $compared_total_pop)*100;
	
	$l2_row_selected_area = pg_fetch_row($location_result_2a,0);
	$selected_total_crime = 0;
	$selected_total_pop = $l2_row_selected_area[1];
	for($i = 2; $i<14; $i++){
		$selected_total_crime += $l2_row_selected_area[$i];
	}
	$selected_crime_rate = ($selected_total_crime / $selected_total_pop)*100;
	
	$diff_l2 = abs($selected_crime_rate-$compared_crime_rate);
	$similarity_l2 = 100-(($diff_l2/$selected_crime_rate)*100);
	$similarity_l2 = cap_similarity($similarity_l2);	
	array_push($query_scores, $similarity_l2);
	array_push($location_scores, $similarity_l2);

	//location3 - compare employment rate
	$l3_row = pg_fetch_row($location_result_3);
	$l3_row_selected_area = pg_fetch_row($location_result_3a,0);
	
	$emp_rate = $l3_row[2]/$l3_row[1];
	$emp_rate = $emp_rate*100;
	$emp_rate = 100 - $emp_rate;
	
	$emp_rate_selected_area = $l3_row_selected_area[2]/$l3_row_selected_area[1];
	$emp_rate_selected_area = $emp_rate_selected_area*100;
	$emp_rate_selected_area = 100 - $emp_rate_selected_area;
	
	$diff_l3 = abs($emp_rate_selected_area - $emp_rate);
	$similarity_l3 = ($diff_l3/$emp_rate_selected_area);
	$similarity_l3 = cap_similarity_1($similarity_l3);
	$similarity_l3 *= 100;
	$similarity_l3 = 100-$similarity_l3;
		
	array_push($query_scores, $similarity_l3);
	array_push($location_scores, $similarity_l3);
	
	//location4 - compare education
	
	$l4_row = pg_fetch_row($location_result_4);
	$l4_row_selected_area = pg_fetch_row($location_result_4a,0);

	$h_educated = $l4_row[1]+$l4_row[2]+$l4_row[3]+$l4_row[4];
	$t_educated = $l4_row[6]-$l4_row[5];
	if($t_educated > 0){
	$h_educated_rate = $h_educated/$t_educated;
	}else{ 
	$h_educated_rate = 0;
	}
	$h_educated_selected_area = $l4_row_selected_area[1]+$l4_row_selected_area[2]+$l4_row_selected_area[3]+$l4_row_selected_area[4];
	$t_educated_selected_area = $l4_row_selected_area[6]-$l4_row_selected_area[5];
	if($t_educated_selected_area > 0){
	$h_educated_rate_selected_area = $h_educated_selected_area/$t_educated_selected_area;
	}else{ 
	$h_educated_rate_selected_area = 0;
	}
	$diff_l4 = abs($h_educated_rate_selected_area - $h_educated_rate);
	$similarity_l4 = ($diff_l4/$h_educated_rate_selected_area);
	
	$similarity_l4 = cap_similarity_1($similarity_l4);

	$similarity_l4 *= 100;
	$similarity_l4 = 100-$similarity_l4;

	array_push($query_scores, $similarity_l4);
	array_push($location_scores, $similarity_l4);

	
	//location5 - compare professional workers 
	
	$l5_row = pg_fetch_row($location_result_5);
	$l5_row_selected_area = pg_fetch_row($location_result_5a,0);

	$pmt_workers  = $l5_row[1]+$l5_row[2];
	$t_workers = $l5_row[3];
	$pmt_rate = $pmt_workers/$t_workers;
	
	$pmt_workers_selected_area  = $l5_row_selected_area[1]+$l5_row_selected_area[2];
	$t_workers_selected_area = $l5_row_selected_area[3];
	$pmt_rate_selected_area = $pmt_workers_selected_area/$t_workers_selected_area;
	if($pmt_rate_selected_area == 0){
		$pmt_rate_selected_area = 1;
	}
	$diff_l5 = abs($pmt_rate_selected_area - $pmt_rate);
	$similarity_l5 = ($diff_l5/$pmt_rate_selected_area);
	$similarity_l5 = cap_similarity_1($similarity_l5);
	$similarity_l5 *= 100;
	$similarity_l5 = 100-$similarity_l5;
	
	array_push($query_scores, $similarity_l5);
	array_push($location_scores, $similarity_l5);
	
	//extra location 1 - compare broadband
	$lx1_row = pg_fetch_row($location_extra_result_1);
	$lx1_row_selected_area = pg_fetch_row($location_extra_result_1a,0);
	
	$num_all_homes = $lx1_row[5] - $lx1_row[4];
	$num_selected_homes = $lx1_row[1];
	$compare_home_percentage = 	($num_selected_homes/$num_all_homes)*100;
	
	$num_all_homes = $lx1_row_selected_area[5] - $lx1_row_selected_area[4];
	$num_selected_homes = $lx1_row_selected_area[1];
	$selected_home_percentage = ($num_selected_homes/$num_all_homes)*100;
	
	$diff_lx1 = abs($selected_home_percentage-$compare_home_percentage);
	$similarity_lx1 = 100-(($diff_lx1/$selected_home_percentage)*100);
	$similarity_lx1 = cap_similarity($similarity_lx1);
	
	array_push($query_scores, $similarity_lx1);	
	array_push($location_scores, $similarity_lx1);

	//extra location 2 -compare ag workers
	
	$lx2_row = pg_fetch_row($location_extra_result_2);
	$lx2_row_selected_area = pg_fetch_row($location_extra_result_2a,0);

	$amu_workers  = $lx2_row[1]+$lx2_row[2]+$lx2_row[3]+$lx2_row[4]+$lx2_row[5];
	$t_workers = $lx2_row[7];
	$amu_rate = $amu_workers/$t_workers;
	
	$amu_workers_selected_area  = $lx2_row_selected_area[1]+$lx2_row_selected_area[2]+$lx2_row_selected_area[3]+$lx2_row_selected_area[4]+$lx2_row_selected_area[5];
	$t_workers_selected_area = $lx2_row_selected_area[7];
	$amu_rate_selected_area = $amu_workers_selected_area/$t_workers_selected_area;
	$diff_lx2 = abs($amu_rate_selected_area - $amu_rate);
	if($amu_rate_selected_area == 0){
		$amu_rate_selected_area = 1;
	}
	$similarity_lx2 = ($diff_lx2/$amu_rate_selected_area);	
	$similarity_lx2 = cap_similarity_1($similarity_lx2);
	$similarity_lx2 *= 100;
	$similarity_lx2 = 100-$similarity_lx2;
	
	array_push($query_scores, $similarity_lx2);
	array_push($location_scores, $similarity_lx2);
	
	
	//=============================================================================================================
	$healthcare_scores=array();
	
	//hc1 - compare hospital distances
	$hc1_row = pg_fetch_row($healthcare_result_1);
	$hc1_row_selected_area = pg_fetch_row($healthcare_result_1a,0);
	
	$selected_dist = $hc1_row_selected_area[1]+1;
	$compare_dist = $hc1_row[1]+1;
	
	$hos_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_hc1 = $hos_dis_difference/$add_distances;
	$similarity_hc1 = 100-($diff_hc1*100)/2;
	
	array_push($query_scores, $similarity_hc1);
	array_push($healthcare_scores, $similarity_hc1);
	
	//hc2 - compare doctor distances
	$hc2_row = pg_fetch_row($healthcare_result_2);
	$hc2_row_selected_area = pg_fetch_row($healthcare_result_2a,0);
	
	$selected_dist = $hc2_row_selected_area[1]+1;
	$compare_dist = $hc2_row[1]+1;
	
	$doc_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_hc2 = $doc_dis_difference/$add_distances;
	$similarity_hc2 = 100-($diff_hc2*100)/2;
	
	array_push($query_scores, $similarity_hc2);
	array_push($healthcare_scores, $similarity_hc2);

	//hc3 compare pharmacy distances
	$hc3_row = pg_fetch_row($healthcare_result_3);
	$hc3_row_selected_area = pg_fetch_row($healthcare_result_3a,0);
	
	$selected_dist = $hc3_row_selected_area[1]+1;
	$compare_dist = $hc3_row[1]+1;
	
	$pharm_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_hc3 = $pharm_dis_difference/$add_distances;
	$similarity_hc3 = 100-($diff_hc3*100)/3;
	
	array_push($query_scores, $similarity_hc3);
	array_push($healthcare_scores, $similarity_hc3);

	//hc extra 1 - compare dentist distances
	$hcx1_row = pg_fetch_row($healthcare_extra_result_1);
	$hcx1_row_selected_area = pg_fetch_row($healthcare_extra_result_1a,0);
	
	$selected_dist = $hcx1_row_selected_area[1]+1;
	$compare_dist = $hcx1_row[1]+1;
	
	$dent_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_hcx1 = $dent_dis_difference/$add_distances;
	$similarity_hcx1 = 100-($diff_hcx1*100)/2;
	
	array_push($query_scores, $similarity_hcx1);
	array_push($healthcare_scores, $similarity_hcx1);
	
	//hc extra 2 - compare nursing homes distances
	$hcx2_row = pg_fetch_row($healthcare_extra_result_2);
	$hcx2_row_selected_area = pg_fetch_row($healthcare_extra_result_2a,0);
	
	$selected_dist = $hcx2_row_selected_area[1]+1;
	$compare_dist = $hcx2_row[1]+1;
	
	$nursing_home_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_hcx2 = $nursing_home_dis_difference/$add_distances;
	$similarity_hcx2 = 100-($diff_hcx2*100)/2;
	
	array_push($query_scores, $similarity_hcx2);
	array_push($healthcare_scores, $similarity_hcx2);
	
	//=============================================================================================================
	$education_scores=array();
	
	//education
	//education 1 - compare schools
	$ed1_row = pg_fetch_row($education_result_1);
	$ed1_row_selected_area = pg_fetch_row($education_result_1a,0);
	
	$selected_ed1 = $ed1_row_selected_area[1]+1; 
	$compare_ed1 = $ed1_row[1]+1;
	
	$diff1 = abs($selected_ed1- $compare_ed1);
	$diff2 = ($selected_ed1+ $compare_ed1)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_ed1 = 100-($diff3/2);
	if($similarity_ed1 < 0){
		$similarity_ed1 =0;
	}
	
	
	array_push($query_scores, $similarity_ed1);
	array_push($education_scores, $similarity_ed1);

	
	//education 2 - compare university distances

	$ed2_row = pg_fetch_row($education_result_2);
	$ed2_row_selected_area = pg_fetch_row($education_result_2a,0);
	
	$selected_dist = $ed2_row_selected_area[1]+1;
	$compare_dist = $ed2_row[1]+1;
	
	$uni_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_ed2 = $uni_dis_difference/$add_distances;
	$similarity_ed2 = 100-($diff_ed2*100)/2;
	
	array_push($query_scores, $similarity_ed2);
	array_push($education_scores, $similarity_ed2);

	
	//education extra 1 - compare preschools
	$edx1_row = pg_fetch_row($education_extra_result_1);
	$edx1_row_selected_area = pg_fetch_row($education_extra_result_1a,0);
	
	$selected_edx1 = $edx1_row_selected_area[1]+1; 
	$compare_edx1 = $edx1_row[1]+1;
	
	$diff1 = abs($selected_edx1- $compare_edx1);
	$diff2 = ($selected_edx1+ $compare_edx1)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_edx1 = 100-($diff3/2);
	$similarity_edx1 = cap_similarity($similarity_edx1);
	array_push($query_scores, $similarity_edx1);
	array_push($education_scores, $similarity_edx1);
	
	//education extra 2-compare library
	$edx2_row = pg_fetch_row($education_extra_result_2);
	$edx2_row_selected_area = pg_fetch_row($education_extra_result_2a,0);
	
	$selected_edx2 = $edx2_row_selected_area[1]+1; 
	$compare_edx2 = $edx2_row[1]+1;
	
	$diff1 = abs($selected_edx2- $compare_edx2);
	$diff2 = ($selected_edx2+ $compare_edx2)/2;
	if($diff2 == 0){
		$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_edx2 = 100-($diff3/2);
	$similarity_edx2 = cap_similarity($similarity_edx2);

	array_push($query_scores, $similarity_edx2);
	array_push($education_scores, $similarity_edx2);

	//=============================================================================================================
	
	$amenities_scores=array();
	//amenities 1 - compare leisure  
	$am1_row = pg_fetch_row($amenities_result_1);
	$am1_row_selected_area = pg_fetch_row($amenities_result_1a,0);
	
	$selected_am1 = $am1_row_selected_area[1]+1; 
	$compare_am1 = $am1_row[1]+1;
	
	$diff1 = abs($selected_am1- $compare_am1);
	$diff2 = ($selected_am1+ $compare_am1)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_am1 = 100-($diff3/2);
	$similarity_am1 = cap_similarity($similarity_am1);

	
	array_push($query_scores, $similarity_am1);
	array_push($amenities_scores, $similarity_am1);
	
	//amenities 2 - compare sports
	$am2_row = pg_fetch_row($amenities_result_2);
	$am2_row_selected_area = pg_fetch_row($amenities_result_2a,0);
	
	$selected_am2 = $am2_row_selected_area[1]+1; 
	$compare_am2 = $am2_row[1]+1;
	
	$diff1 = abs($selected_am2- $compare_am2);
	$diff2 = ($selected_am2+ $compare_am2)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_am2 = 100-($diff3/2);
	$similarity_am2 = cap_similarity($similarity_am2);

	
	array_push($query_scores, $similarity_am2);
	array_push($amenities_scores, $similarity_am2);
	
	//amenities 3 - compare bars + restaraunts
	$am3_row = pg_fetch_row($amenities_result_3);
	$am3_row_selected_area = pg_fetch_row($amenities_result_3a,0);
	
	$selected_am3 = $am3_row_selected_area[1]+1; 
	$compare_am3 = $am3_row[1]+1;
	
	$diff1 = abs($selected_am3- $compare_am3);
	$diff2 = ($selected_am3+ $compare_am3)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_am3 = 100-($diff3/2);
	$similarity_am3 = cap_similarity($similarity_am3);
	
	array_push($query_scores, $similarity_am3);
	array_push($amenities_scores, $similarity_am3);
	
	//amenities 4 - compare grocey stores
	$am4_row = pg_fetch_row($amenities_result_4);
	$am4_row_selected_area = pg_fetch_row($amenities_result_4a,0);

	$selected_am4 = $am4_row_selected_area[1]+1; 
	$compare_am4 = $am4_row[1]+1;
	
	$diff1 = abs($selected_am4- $compare_am4);
	$diff2 = ($selected_am4+ $compare_am4)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_am4 = 100-($diff3/2);
	$similarity_am4 = cap_similarity($similarity_am4);

	array_push($query_scores, $similarity_am4);
	array_push($amenities_scores, $similarity_am4);
	
	//amenities 5 - compare retail stores 
	$am5_row = pg_fetch_row($amenities_result_5);
	$am5_row_selected_area = pg_fetch_row($amenities_result_5a,0);

	$selected_am5 = $am5_row_selected_area[1]+1; 
	$compare_am5 = $am5_row[1]+1;
	
	$diff1 = abs($selected_am5- $compare_am5);
	$diff2 = ($selected_am5+ $compare_am5)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_am5 = 100-($diff3/2);
	$similarity_am5 = cap_similarity($similarity_am5);

	array_push($query_scores, $similarity_am5);
	array_push($amenities_scores, $similarity_am5);
	
	//amenities 6 -  compare tourist locations
	$am6_row = pg_fetch_row($amenities_result_6);
	$am6_row_selected_area = pg_fetch_row($amenities_result_6a,0);

	$selected_am6 = $am6_row_selected_area[1]+1; 
	$compare_am6 = $am6_row[1]+1;
	
	$diff1 = abs($selected_am6- $compare_am6);
	$diff2 = ($selected_am6+ $compare_am6)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_am6 = 100-($diff3/2);
	$similarity_am6 = cap_similarity($similarity_am6);

	array_push($query_scores, $similarity_am6);
	array_push($amenities_scores, $similarity_am6);
	
	//extra amenities 1 - compare firestations distances
	$amx1_row = pg_fetch_row($amenities_extra_result_1);
	$amx1_row_selected_area = pg_fetch_row($amenities_extra_result_1a,0);
	
	$selected_dist = $amx1_row_selected_area[1]+1;
	$compare_dist = $amx1_row[1]+1;
	
	$firestation_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_amx1 = $firestation_dis_difference/$add_distances;
	$similarity_amx1 = 100-($diff_amx1*100)/2;
	
	array_push($query_scores, $similarity_amx1);
	array_push($amenities_scores, $similarity_amx1);


	//extra amenities 2 compare post_office distances
	$amx2_row = pg_fetch_row($amenities_extra_result_2);
	$amx2_row_selected_area = pg_fetch_row($amenities_extra_result_2a,0);
	
	$selected_dist = $amx2_row_selected_area[1]+1;
	$compare_dist = $amx2_row[1]+1;
	
	$postoffice_dis_difference = abs($selected_dist- $compare_dist);
	$add_distances = ($selected_dist+$compare_dist)/2;
	$diff_amx2 = $postoffice_dis_difference/$add_distances;
	$similarity_amx2 = 100-($diff_amx2*100)/2;
	
	array_push($query_scores, $similarity_amx2);
	array_push($amenities_scores, $similarity_amx2);

	//=============================================================================================================

	//people
	$people_scores=array();
	//people1 - compare irish speakers
		
	$p1_row = pg_fetch_row($people_result_1);
	$p1_row_selected_area = pg_fetch_row($people_result_1a,0);

	$irish_speakers = $p1_row[1]/$p1_row[2];
	$irish_speakers = $irish_speakers*100;
	$p1_score = ($irish_speakers) * 100;
	
	$irish_speakers_selected_area = $p1_row_selected_area[1]/$p1_row_selected_area[2];
	$irish_speakers_selected_area = $irish_speakers_selected_area*100;
	$p1_scorea = ($irish_speakers_selected_area) * 100;
	
	$diff_p1 = abs($p1_scorea - $p1_score);
	$similarity_p1 = ($diff_p1/$p1_scorea);
	
	$similarity_p1 = cap_similarity_1($similarity_p1);
	$similarity_p1 *= 100;
	$similarity_p1 = 100-$similarity_p1;
	
	array_push($query_scores, $similarity_p1);
	array_push($people_scores, $similarity_p1);
	
	//people2 - compare age groups
	$p2_row = pg_fetch_row($people_result_2);
	$p2_row_selected_area = pg_fetch_row($people_result_2a,0);

	$families = $p2_row[1]+$p2_row[3]+$p2_row[5]+$p2_row[7]+$p2_row[8];
	$students = $p2_row[2]+$p2_row[4];
	$retired = $p2_row[6];
	$total_people = $p2_row[9];

	
	$families_selected_area = $p2_row_selected_area[1]+$p2_row_selected_area[3]+$p2_row_selected_area[5]+$p2_row_selected_area[7]+$p2_row_selected_area[8];
	$students_selected_area = $p2_row_selected_area[2]+$p2_row_selected_area[4];
	$retired_selected_area = $p2_row_selected_area[6];
	$total_people_selected_area = $p2_row_selected_area[9];

	
	 $diff1 = abs(($families_selected_area/$total_people_selected_area) - ($families/$total_people));
	 $diff2 = abs(($students_selected_area/$total_people_selected_area) - ($students/$total_people));
	 $diff3 = abs(($retired_selected_area/$total_people_selected_area) - ($retired/$total_people));
	 
	 $totaldiff = $diff1 + $diff2 + $diff3;
	 $similarity_p2 = 100-($totaldiff*100);
	 $similarity_p2 = cap_similarity($similarity_p2);
	 array_push($query_scores, $similarity_p2);
	 array_push($people_scores, $similarity_p2);
	
	//people3 - compare types of people
	$p3_row = pg_fetch_row($people_result_3);
	$p3_row_selected_area = pg_fetch_row($people_result_3a,0);

	$irish_white = $p3_row[1];
	$irish_traveller = $p3_row[2];
	$other_white = $p3_row[3];
	$black = $p3_row[4];
	$asian = $p3_row[5];
	$other = $p3_row[6];
	$total_people = $p3_row[8] - $p3_row[7];

	$irish_white_selected_area = $p3_row_selected_area[1];
	$irish_traveller_selected_area = $p3_row_selected_area[2];
	$other_white_selected_area = $p3_row_selected_area[3];
	$black_selected_area = $p3_row_selected_area[4];
	$asian_selected_area = $p3_row_selected_area[5];
	$other_selected_area = $p3_row_selected_area[6];
	$total_people_selected_area = $p3_row_selected_area[8] - $p3_row_selected_area[7];
	
	$diff1 = abs(($irish_white_selected_area/$total_people_selected_area) - ($irish_white/$total_people));
	$diff2 = abs(($irish_traveller_selected_area/$total_people_selected_area) - ($irish_traveller/$total_people));
	$diff3 = abs(($other_white_selected_area/$total_people_selected_area) - ($other_white/$total_people));
	$diff4 = abs(($black_selected_area/$total_people_selected_area) - ($black/$total_people));
	$diff5 = abs(($asian_selected_area/$total_people_selected_area) - ($asian/$total_people));
	$diff6 = abs(($other_selected_area/$total_people_selected_area) - ($other/$total_people));
	
	$totaldiff = $diff1 + $diff2 + $diff3 + $diff4 + $diff5 + $diff6;
	$similarity_p3 = 100-($totaldiff*100);
	
	 $similarity_p3 = cap_similarity($similarity_p3);
	array_push($query_scores, $similarity_p3);
	array_push($people_scores, $similarity_p3);

	//people4 - compare religon
	$p4_row = pg_fetch_row($people_result_4);
	$p4_row_selected_area = pg_fetch_row($people_result_4a,0);

	$catholic = $p4_row[1];
	$other = $p4_row[2];
	$none = $p4_row[3];
	$total_people = $p4_row[5]-$p4_row[4];

	$catholic_selected_area = $p4_row_selected_area[1];
	$other_selected_area = $p4_row_selected_area[2];
	$none_selected_area = $p4_row_selected_area[3];
	$total_people_selected_area = $p4_row_selected_area[5]-$p4_row_selected_area[4];
	
	$diff1 = abs(($catholic_selected_area/$total_people_selected_area) - ($catholic_selected_area/$total_people));
	$diff2 = abs(($other_selected_area/$total_people_selected_area) - ($other_selected_area/$total_people));
	$diff3 = abs(($none_selected_area/$total_people_selected_area) - ($none_selected_area/$total_people));
	
	$totaldiff = $diff1 + $diff2 + $diff3;
	$similarity_p4 = 100-($totaldiff*100);
	$similarity_p4 = cap_similarity($similarity_p4);
	
	array_push($query_scores, $similarity_p4);
	array_push($people_scores, $similarity_p4);
	
	//people extra - health of the people compare
	$px1_row = pg_fetch_row($people_extra_result_1);
	$px1_row_selected_area = pg_fetch_row($people_extra_result_1a,0);
	
	$num_all_people = $px1_row[4] - $px1_row[3];
	$num_selected_people = $px1_row[1]+$px1_row[2];
	$compare_person_percentage = ($num_selected_people/$num_all_people)*100;
	
	$num_all_people = $px1_row_selected_area[4] - $px1_row_selected_area[3];
	$num_selected_people = $px1_row_selected_area[1]+$px1_row_selected_area[2];
	$selected_person_percentage = ($num_selected_people/$num_all_people)*100;
	
	$diff_px1 = abs($selected_person_percentage-$compare_person_percentage);
	$similarity_px1 = 100-(($diff_px1/$selected_person_percentage)*100);
	
	$similarity_px1 = cap_similarity($similarity_px1);

	array_push($query_scores, $similarity_px1);
	array_push($people_scores, $similarity_px1);	
		
	//=============================================================================================================
	
	$transport_scores=array();
	//Transport1 - compare the commute time
	$t1_row_selected_area = pg_fetch_row($transport_result_1a,0);
	$t1_row = pg_fetch_row($transport_result_1);
	
	$total_people = $t1_row[8]-$t1_row[7];
	$total_people_selected_area = $t1_row_selected_area[8]-$t1_row_selected_area[7];
	
	$t1diff = 0;
	$tt1diff=0;
		
	for ($x = 1; $x < 7; $x++) {
		$pc_selected_area = $t1_row_selected_area[$x]/$total_people_selected_area;
		$pc = $t1_row[$x]/$total_people;		
		$t1diff = ABS($pc_selected_area-$pc);
		$tt1diff += $t1diff;
		
	}
	$similarity_t1 = ABS(1-$tt1diff)*100;
		
	array_push($query_scores, $similarity_t1);
	array_push($transport_scores, $similarity_t1);
		
	//Transport2 - compare transport usage in area

	$t2_row = pg_fetch_row($transport_result_2);
	$t2_row_selected_area = pg_fetch_row($transport_result_2a,0);

	$total_public_transport_users = $t2_row[1] + $t2_row[2];
	$total_people = $t2_row[3];
	$transport_users = $total_public_transport_users/$total_people;
	$transport_users = $transport_users*100;
		
	$total_public_transport_usersa = $t2_row_selected_area[1] + $t2_row_selected_area[2];
	$total_people_selected_area = $t2_row_selected_area[3];
	$transport_usersa = $total_public_transport_usersa/$total_people_selected_area;
	$transport_usersa = $transport_usersa*100;
		
	$diff = abs($transport_usersa - $transport_users);
		
	$diff_t2 = abs($transport_usersa - $transport_users);
	$similarity_t2 = ($diff_t2/$transport_usersa);
	$similarity_t2 = cap_similarity_1($similarity_t2);
	$similarity_t2 *= 100;
	$similarity_t2 = 100-$similarity_t2;

	array_push($query_scores, $similarity_t2);
	array_push($transport_scores, $similarity_t2);
		
	//transport 3 - compare number of stops
	$t3_row = pg_fetch_row($transport_result_3);
	$t3_row_selected_area = pg_fetch_row($transport_result_3a,0);

	$selected_t3 = $t3_row_selected_area[1]+1; 
	$compare_t3 = $t3_row[1]+1;
	
	$diff1 = abs($selected_t3- $compare_t3);
	$diff2 = ($selected_t3+ $compare_t3)/2;
	if($diff2 == 0){
	$diff2 = 1;
	}
	$diff3 = ($diff1/$diff2);
	$diff3 = $diff3 *100;
	
	$similarity_t3 = 100-($diff3/2);
	$similarity_t3 = cap_similarity($similarity_t3);

	array_push($query_scores, $similarity_t3);
	array_push($transport_scores, $similarity_t3);
	
	//transport extra 1
	$tx1_row = pg_fetch_row($transport_extra_result_1);
	$tx1_row_selected_area = pg_fetch_row($transport_extra_result_1a,0);
	
	$num_all_transport = $tx1_row[7] - $tx1_row[6];
	$num_selected_transport = $tx1_row[2] + $tx1_row[3] +  $tx1_row[4] +  $tx1_row[5];
	$compare_transport_percentage = ($num_selected_transport/$num_all_transport)*100;
	
	$num_all_transport = $tx1_row_selected_area[7] - $tx1_row_selected_area[6];
	$num_selected_transport = $tx1_row_selected_area[2] + $tx1_row_selected_area[3] +  $tx1_row_selected_area[4] +  $tx1_row_selected_area[5];
	$selected_transport_percentage = ($num_selected_transport/$num_all_transport)*100;
	
	$diff_tx1 = abs($selected_transport_percentage-$compare_transport_percentage);
	$similarity_tx1 = 100-(($diff_tx1/$selected_transport_percentage)*100);
	$similarity_tx1 = cap_similarity($similarity_tx1);

	array_push($query_scores, $similarity_tx1);
	array_push($transport_scores, $similarity_tx1);	
	
	//=============================================================================================================/
		//individual unweighted scores for each section
	$housing_scores_total = round( array_sum($housing_scores) / count($housing_scores));
	$location_scores_total = round( array_sum($location_scores) / count($location_scores));
	$healthcare_scores_total = round( array_sum($healthcare_scores) / count($healthcare_scores));
	$education_scores_total = round( array_sum($education_scores) / count($education_scores));
	$amenities_scores_total = round( array_sum($amenities_scores) / count($amenities_scores));
	$people_scores_total = round( array_sum($people_scores) / count($people_scores));
	$transport_scores_total = round( array_sum($transport_scores) / count($transport_scores));

	//overall score
	$rounded_score = round( array_sum($query_scores) / count($query_scores));

	//=============================================================================================================
	//strings added to the geojson array to construct the geojson file for the output

	if($count < $num_lines) { 
		array_push($geoJSON_array,"{ \"type\": \"Feature\", \"properties\": { \"gid\": $row[0],  \"score\": $rounded_score , \"countyname\": \"$row[1]\" , \"edname\": \"$row[3]\", \"housing_score\": \"$housing_scores_total\",\"location_score\": \"$location_scores_total\",\"healthcare_score\": \"$healthcare_scores_total\",\"education_score\": \"$education_scores_total\",\"amenities_score\": \"$amenities_scores_total\",\"people_score\": \"$people_scores_total\",\"transport_score\": \"$transport_scores_total\" }, \"geometry\": $row[2]},\n");
	}
	else{
		array_push($geoJSON_array,"{ \"type\": \"Feature\", \"properties\": { \"gid\": $row[0],  \"score\": $rounded_score , \"countyname\": \"$row[1]\" , \"edname\": \"$row[3]\" , \"housing_score\": \"$housing_scores_total\",\"location_score\": \"$location_scores_total\",\"healthcare_score\": \"$healthcare_scores_total\",\"education_score\": \"$education_scores_total\",\"amenities_score\": \"$amenities_scores_total\",\"people_score\": \"$people_scores_total\",\"transport_score\": \"$transport_scores_total\" }, \"geometry\": $row[2]}\n");
	}
//adds score to the sorting array	
$sort_scores[$row[0]] = $rounded_score;

}
//adds the final piece to the geojson array 
array_push($geoJSON_array,"]}");

//sorts the scores
arsort($sort_scores);
$top_scores=array();

//creates the top 15 scores + the selected area
$count = 0;
foreach($sort_scores as $x => $x_value) {
 
    $count++;
		if($count<=16){
			array_push($top_scores, "$x");
			//break;
		}
		if($count==16){
			break;
		}
	
}

$out = implode($geoJSON_array);
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
  <h2 id ="area_selected"> </h2>
  <ul class="list-group">
    <li class="list-group-item"><h1 id="list00">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 1); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list01">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 2); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list02">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 3); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list03">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 4); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list04">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 5); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list05">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 6); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list06">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 7); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list07">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 8); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list08">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 9); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list09">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 10); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list10">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 11); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list11">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 12); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list12">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 13); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list13">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 14); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
    <li class="list-group-item"><h1 id="list14">Name :: Score</h1><button class="btn" onclick="zoomToLocationOnMap(geojson, 15); document.getElementById('map').scrollIntoView();">Click Here to View on Map</button></li>
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

//function to load the values into the top 15 list + selected area
window.onload = function() {
	var list = ["area_selected","list00","list01","list02","list03","list04","list05","list06","list07","list08","list09","list10","list11","list12","list13","list14"];
	for(var i =0; i<list.length; i++){
		area_ID = parseInt(top15List[i]);
		geojson.eachLayer(function(layer) {
  		if (layer.feature.properties.layerID === area_ID) {
        	
        	var element = document.getElementById(list[i]);
        	if(i!=0){
			element.innerHTML = layer.feature.properties.edname + ", " + layer.feature.properties.countyname + "<br> Score " + layer.feature.properties.score;
			}
			else{
			element.innerHTML = "Top 15 areas that are similar to " + layer.feature.properties.edname + ", " + layer.feature.properties.countyname;
			}
    	}
    });
  }
}

</script>

</body>
</html>
