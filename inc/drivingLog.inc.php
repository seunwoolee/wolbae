<?

class DrivingLog
{

	static function updateTotalDistance($db, $modifyDrivingLogSeq)
	{
		$db->que = "SELECT userUid FROM drivingLog WHERE seq=". $modifyDrivingLogSeq;
		$db->query();
		if($db->affected_rows() > 0)
		{
			$uid = $db->getOne();
			$db->que = "SELECT * FROM drivingLog WHERE userUid='". $uid. "' ORDER BY startDate DESC, stopDistance DESC, seq DESC LIMIT 1";
			$db->query();

			$row = $db->getRow();
			if($row["seq"] == $modifyDrivingLogSeq)
			{
				$DATA["totalDistance"] = $row["stopDistance"];
				$db->Update("user", $DATA, "WHERE uid='". $uid. "'", "user update error");
			}
		}
	}


	static function getPurposeName($purpose)
	{
		if($purpose == "a")
		{
			return "일반업무";
		}
		else if($purpose == "e")
		{
			return "출·퇴근";
		}
		else if($purpose == "g")
		{
			return "비업무용";
		}
		else
		{
			return "미입력";
		}
	}


	function getDistance($lat1, $lon1, $lat2, $lon2)
	{

	}

	function getSpeed($distance, $beginTime, $endTime)
	{

	}
}
	