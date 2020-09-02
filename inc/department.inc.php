<?
class Department
{
    var $db;
	var $companySeq;
	var $departments;
	var $tree;

	function Department($db, $companySeq)
	{
		$this->db = $db;
		$this->companySeq = $companySeq;
	}


	function loadData()
	{
		$this->db->que = "SELECT * FROM department WHERE companySeq=". $this->companySeq. " ORDER BY depth ASC, sort ASC";
		$this->db->query();
		$this->departments = $this->db->getRows();
		$this->createTree(0);
	}

	function getDepartment($seq)
	{
		$this->db->que = "SELECT * FROM department WHERE seq=". $seq;
		$this->db->query();
		return $this->db->getRow();
	}

	function getParentSeq($seq)
	{
		$department = $this->getDepartment($seq);
		return $department["parentSeq"];
	}

	function remove($seq)
	{

		$this->db->que = "SELECT COUNT(*) FROM user WHERE departmentSeq=". $seq. " AND enabled != 'B'";
		$this->db->query();
		if($this->db->getOne() > 0)
		{
			return "삭제할 부서에 사용자가 있습니다. 타 부서로 이동 후 삭제해 주세요.";
		}
		else
		{
			$this->db->que = "SELECT COUNT(*) FROM department WHERE parentSeq=". $seq;
			$this->db->query();
			if($this->db->getOne() > 0)
			{
				return "삭제할 부서에 하위 부서가 있습니다. 하위 부서를 제거후 삭제해 주세요.";
			}
			else
			{
				$this->updateSort($seq);
				$this->db->Delete("department", "where seq=". $seq, "delete error");
			}
		}
	}

	function add($name, $parentSeq)
	{
		$maxSort = $this->getMaxSort($parentSeq);
		$DATA["companySeq"]		= $this->companySeq;
		$DATA["name"]					= $name;
		$DATA["fullName"]				= $name;
		$DATA["sort"]					= $maxSort + 1;
		$DATA["parentSeq"]			= $parentSeq;
		$DATA["depth"]				= 0;

		if($parentSeq > 0)
		{
			$parent						= $this->getDepartment($parentSeq);
			$DATA["depth"]			= $parent["depth"] + 1;
			$DATA["fullName"]			= $parent["fullName"]. "/". $name;
		}
		
		$this->db->Insert("department", $DATA, "insert error");
	}

	function modify($seq, $name, $parentSeq)
	{
		$this->loadData();
		if($this->isMyGroup($seq, $parentSeq) == true)
		{
			return "현재 부서 또는 하위 부서로는 이동할 수 없습니다.";
		}
		else
		{
			$before = $this->getDepartment($seq);

			if($before["parentSeq"] != $parentSeq)
			{
				//기존 그룹 sort 조정
				$this->updateSort($seq);

				//하위 부서까지 Depth 일괄 증가/감소
				$this->modifyGroupDepth($seq, $parentSeq);


				$maxSort = $this->getMaxSort($parentSeq);
				$DATA["name"]					= $name;
				$DATA["sort"]					= $maxSort + 1;
				$DATA["parentSeq"]			= $parentSeq;
				$this->db->Update("department", $DATA, "where seq=". $seq, "update error");
				
			}
			else
			{
				$DATA["name"]					= $name;
				$this->db->Update("department", $DATA, "where seq=". $seq, "update error");
			}

			//하위 부서까지 fullName 일괄 수정 
			$this->modifyGroupFullName($seq, $name, $parentSeq);
		}
	}

	//하위 부서까지 fullName 일괄 수정 
	function modifyGroupFullName($seq, $name, $parentSeq)
	{
		$before = $this->getDepartment($seq);
		$groupSeqList = $this->getGroupSeqList($seq);

		if($parentSeq > 0)
		{
			$parent						= $this->getDepartment($parentSeq);
			$afterFullName				= $parent["fullName"]. "/". $name;
		}
		else
		{
			$afterFullName				= $name;
		}
		
		$this->db->que = "UPDATE department SET ";
		$this->db->que .= "fullName=CONCAT('". $afterFullName. "', SUBSTRING(fullName, ". (mb_strlen($before["fullName"], "UTF-8")+1). ")) ";
		$this->db->que .= "WHERE seq in(". $groupSeqList. ")";
		$this->db->query();
	}

	//하위 부서까지 Depth 일괄 증가/감소
	function modifyGroupDepth($seq, $parentSeq)
	{
		if($parentSeq > 0)
		{
			$parent						= $this->getDepartment($parentSeq);
			$afterDepth					= $parent["depth"] + 1;
		}
		else
		{
			$afterDepth					= 0;
		}

		$before = $this->getDepartment($seq);
		$groupSeqList = $this->getGroupSeqList($seq);

		if($afterDepth > $before["depth"])
		{
			$increase = $afterDepth - $before["depth"];
			$this->db->que = "UPDATE department SET depth = depth + ". $increase. " WHERE seq in(". $groupSeqList. ")";
			$this->db->query();
		}
		else if($afterDepth < $before["depth"])
		{
			$decrease = $before["depth"] - $afterDepth;
			$this->db->que = "UPDATE department SET depth = depth - ". $decrease. " WHERE seq in(". $groupSeqList. ")";
			$this->db->query();
		}
	}


	function up($seq)
	{
		$choice = $this->getDepartment($seq);
		$this->db->que = "SELECT * FROM department WHERE companySeq=". $this->companySeq. " AND parentSeq=". $choice["parentSeq"]. " AND sort < ". $choice["sort"]. " ORDER BY sort DESC LIMIT 1";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$prev = $this->db->getRow();
			$DATA["sort"] = $prev["sort"];
			$this->db->Update("department", $DATA, "where seq=". $choice["seq"], "sort update error");

			$DATA["sort"] = $choice["sort"];
			$this->db->Update("department", $DATA, "where seq=". $prev["seq"], "sort update error");
		}
	}


	function down($seq)
	{
		$choice = $this->getDepartment($seq);
		$this->db->que = "SELECT * FROM department WHERE companySeq=". $this->companySeq. " AND parentSeq=". $choice["parentSeq"]. " AND sort > ". $choice["sort"]. " ORDER BY sort ASC LIMIT 1";
		$this->db->query();
		if($this->db->affected_rows() > 0)
		{
			$next = $this->db->getRow();
			$DATA["sort"] = $next["sort"];
			$this->db->Update("department", $DATA, "where seq=". $choice["seq"], "sort update error");

			$DATA["sort"] = $choice["sort"];
			$this->db->Update("department", $DATA, "where seq=". $next["seq"], "sort update error");
		}
	}


	function getMaxSort($parentSeq)
	{
		$this->db->que = "SELECT MAX(sort) FROM department WHERE companySeq=". $this->companySeq. " AND parentSeq=". $parentSeq;
		$this->db->query();
		$max = $this->db->getOne();
		if(empty($max))
		{
			return 0;
		}
		else
		{
			return $max;
		}
	}

	function updateSort($seq)
	{
		//현재부서 다음 부서들 sort 1씩 마이너스 처리
		$department = $this->getDepartment($seq);
		$this->db->que = "UPDATE department SET sort=sort-1 WHERE companySeq=". $this->companySeq. " AND parentSeq=". $department["parentSeq"]. " AND sort > ". $department["sort"];
		$this->db->query();
	}


	//트리구조 만들기
	function createTree($parentSeq)
	{
		$count = count($this->departments);
		for($i=0; $i<$count;)
		{
			if(empty($this->departments[$i]))
			{
				return;
			}
			else
			{
				if($this->departments[$i]["parentSeq"] == $parentSeq)
				{
					$this->tree[] = $this->departments[$i];
					$seq = $this->departments[$i]["seq"];
					array_splice($this->departments, $i, 1);
					$this->createTree($seq);
				}
				else
				{
					$i++;
				}
			}
		}
	}


	function isMyGroup($groupSeq, $seq)
	{
		$group = $this->getGroup($groupSeq);
		$count = count($group);
		for($i=0; $i<$count; $i++)
		{
			if($seq == $group[$i]["seq"])
			{
				return true;
				break;
			}
		}
		
		return false;
	}


	//특정 부서 선택시 해당 부서 및 하위부서 추출
	function getGroup($seq)
	{
		$count = count($this->tree);
		$depth = -1;
		$group = null;

		for($i=0; $i<$count; $i++)
		{
			if($depth > -1)
			{
				if($this->tree[$i]["depth"] <= $depth)
				{
					return $group;
				}
				$group[] = $this->tree[$i];
			}


			if($seq == $this->tree[$i]["seq"])
			{
				$depth = $this->tree[$i]["depth"];
				$group[] = $this->tree[$i];
			}
		}
		
		return $group;
	}

	//특정부서 선택시 하위부서 포함한 모든 seq 추출
	function getGroupSeqList($seq)
	{
		$tree = $this->getGroup($seq);
		$seqList = "";

		$count = count($tree);
		for($i=0; $i<$count; $i++)
		{
			$seqList .= $tree[$i]["seq"]. ",";
		}
		
		$seqList = substr($seqList, 0, -1);
		return $seqList;
	}


	function getTree()
	{
		return $this->tree;
	}


	function getBlanks($count)
	{
		for($i=0; $i<$count; $i++)
		{
			$blanks .= "&nbsp;&nbsp;";
		}

		return $blanks;
	}

	function getTreeSelectBoxOptions($selectedSeq=0)
	{
		$tree = $this->tree;
		$count = count($tree);
		for($i=0; $i<$count; $i++)
		{
			if($tree[$i]["depth"] > 0)
			{
				$name = $this->getBlanks($tree[$i]["depth"]). "└ ". $tree[$i]["name"];
			}
			else
			{
				$name = $tree[$i]["name"];
			}

			if($selectedSeq == $tree[$i]["seq"])
			{
				$selected = "selected";
			}
			else
			{
				$selected = "";
			}


			$LIST .= "<option value='". $tree[$i]["seq"]. "' ". $selected. ">". $name. "</option>\n";
		}
		
		return $LIST;
	}
}
?>