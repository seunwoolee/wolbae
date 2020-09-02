<?
class Paging {
	var $fetchSize = 20;
	var $pageCount = 5;
	var $totalCount = 0;
	var $pageNum = 0;
	var $startRow = 0;
	var $listNum = 0;
	

	function Paging($fetchSize=20, $pageCount=5)
	{
		$this->fetchSize = $fetchSize;
		$this->pageCount = $pageCount;
	}

	function set($totalCount, $pageNum)
	{
		$this->totalCount = $totalCount;

		if($pageNum < 1) {
			$pageNum = 1;
		}

		$this->pageNum = $pageNum;
		$this->startRow = ($pageNum-1) * $this->fetchSize;

		$this->listNum = $totalCount - (($pageNum-1) * $this->fetchSize);
	}

	function getListNum() {
		$listNum = $this->listNum;
		$this->listNum--;

		return $listNum;
	}


	function getPages()
	{
		$totalCount = $this->totalCount;

		$pGroup = (int) ceil($this->pageNum / $this->pageCount) - 1; // 페이지 그룹 (1.2.3 ~ = 0    11.12.13 ~ = 1)
		$pStart = $pGroup * $this->pageCount + 1;
		$pEnd = $pStart + $this->pageCount - 1;


		//이전 버튼
		//------------------------------------------------------------------------------------
		if($pGroup > 0)
		{
			$prePage = $pStart - $this->pageCount;
		}
		else
		{
			$prePage = 1;
		}

		$prevButton = "	<li>
							<a href='javascript:listPaging($prePage)' aria-label='Previous'>
								<span aria-hidden='true'><img src='images/icon/btn_navi_prev.gif' alt='이전 페이지 가기' /></span>
							</a>
						</li>";

		//다음 버튼
		//------------------------------------------------------------------------------------
		$totalPage = ceil($totalCount / $this->fetchSize);
		if($totalPage > $pEnd)
		{
			$nextPage = $pEnd + 1;
		}
		else
		{
			$pEnd = (int) $totalPage;
			if($pEnd < 1)
			{
				$pEnd = 1;
			}

			$nextPage = $pEnd;
		}

		$nextButton = "	<li>
							<a href='javascript:listPaging($nextPage)' aria-label='Next'>
								<span aria-hidden='true'><img src='images/icon/btn_navi_next.gif' alt='다음 페이지 가기' /></span>
							</a>
						</li>";
		// pages
		//------------------------------------------------------------------------------------
		for($i=$pStart; $i<=$pEnd; $i++)
		{

			if($i == $this->pageNum)
			{
				$active = "active";
			}
			else
			{
				$active = "";
			}
			$pages .= "<li class='$active'><a href='javascript:listPaging($i)'>$i</a></li>";
		}

			
		return $prevButton. $pages. $nextButton;
	}

}