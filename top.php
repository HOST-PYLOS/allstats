<?php
/*********************************************
<!-- 
*   	DOTA ALLSTATS
*   
*	Developers: Reinert, Dag Morten, Netbrain, Billbabong, Boltergeist.
*	Contact: developer@miq.no - Reinert
*
*	
*	Please see http://forum.codelain.com/index.php?topic=4752.0
*	and post your webpage there, so I know who's using it.
*
*	Files downloaded from http://code.google.com/p/allstats/
*
*	Copyright (C) 2009  Reinert, Dag Morten , Netbrain, Billbabong, Boltergeist
*
*
*	This file is part of DOTA ALLSTATS.
*
* 
*	 DOTA ALLSTATS is free software: you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation, either version 3 of the License, or
*    (at your option) any later version.
*
*    DOTA ALLSTATS is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with DOTA ALLSTATS.  If not, see <http://www.gnu.org/licenses/>
*
-->
**********************************************/

require_once("functions.php");
require_once("config.php");

if($dbType == 'sqlite')
{
	$games=sqlite_escape_string($_GET["g"]);
	$sortcat=sqlite_escape_string($_GET["s"]);
	$order=sqlite_escape_string($_GET["o"]);
	$offset=sqlite_escape_string($_GET["n"]);
}
else
{
	$games=mysql_real_escape_string($_GET["g"]);
	$sortcat=mysql_real_escape_string($_GET["s"]);
	$order=mysql_real_escape_string($_GET["o"]);
	$offset=mysql_real_escape_string($_GET["n"]);
}

$sql = "select count(*) as count from( select name from gameplayers as gp, dotagames as dg, games as ga,dotaplayers as dp where dg.winner <> 0 and dp.gameid = gp.gameid 
and dg.gameid = dp.gameid and dp.gameid = ga.id and gp.gameid = dg.gameid and gp.colour = dp.colour";

if($ignorePubs)
{
$sql = $sql." and gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and gamestate = '16'";
}

$sql = $sql." group by gp.name 
having count(*) >= $games) 
as h";

if($dbType == 'sqlite')
{
foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$count=$row["count"];
	}	
}
else
{
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$count=$row["count"];
		}
	mysql_free_result($result);
}

	$pages = ceil($count/$topResultSize);

?>
<div class="header" id="header">
	<table width=1016px>
		<tr>
			<td width=25%>
				<table class="rowuh" width = 235px style="float:left">
					<h4>
					<tr>
						<td>
						<?php
						if($offset == 'all')
						{
							print "Showing All Players";
						}
						else
						{
							print "<a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=all\">Show All Players</a>";
						}
						?>
						</td>
					</tr>
					</h4>
				</table>
			</td>
			<td width=50%>
				<h2>Top Players <?php if($ignorePubs){ print "for Private Games";} else if($ignorePrivs){ print "for Public Games";} else { print "for All Games";} ?>:</h2>
			</td>
			<td width=25% class="rowuh">
				<table class="rowuh" width = 235px style="float:right">
				<h4>
				<tr>
					<td colspan=7>
					<?php
					if($offset == 'all')
					{
						print "Show Players Page:";
					}
					else
					{
						$min = $offset*$topResultSize+1;
						$max = $offset*$topResultSize+$topResultSize;
						if($max > $count)
						{
							$max = $count;
						}
						print "Showing Players: ".$min." - ".$max;
					}
					?>
					</td>
				</tr>
				<tr>
				<?php
				if($offset == 'all')
				{
					print "<td width=35px><span style=\"color:#ddd;\"><</span></td>";
					for($counter = 1; $counter < 6; $counter++)
					{
						if($counter <= $pages)
						{
						print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
						}
					}
					print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
				}
				else
				{
					if($offset > 0)
					{
						print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($offset-1)."\"><</a>";
					}
					else
					{
						print "<td width=35px><span style=\"color:#ddd;\"><</span></td>";
					}
					
					if($offset < 2)		//Close to start
					{
						if($offset == 0)
						{
							print "<td width=35px><span style=\"color:#ddd;\">1</span></td>";
							for($counter = 2; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
									print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
						}
						if($offset == 1)
						{
							print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=0\">1</a></td>";
							print "<td width=35px><span style=\"color:#ddd;\">2</span></td>";
							for($counter = 3; $counter < 6; $counter++)
							{
								if($counter-1 < $pages)
								{
								print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								
								}
							}
						}
					}
					else if ($pages-$offset < 3) //Close to end
					{
						if($offset == $pages-1)
						{
							for($counter = $offset-3; $counter < $offset+1; $counter++)
							{
								if($counter >= 1)
								{
								print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".$counter."</span></td>";
						}
						else
						{
							
							for($counter = $offset-2; $counter < $offset+1; $counter++)
							{
								if($counter >= 1)
								{
									print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
								}
							}
							print "<td width=35px><span style=\"color:#ddd;\">".($offset+1)."</span>";
							print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($offset+1)."\">".($offset+2)."</a></td>";
						}
					}
					else
					{
						for($counter = ($offset-1); $counter < ($offset+4); $counter++)
							{
							if($counter == ($offset+1))
							{
								print "<td width=35px><span style=\"color:#ddd;\">".$counter."</span></td>";
							}
							else
							{
								print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($counter-1)."\">".$counter."</a></td>";
							}
						}
					}
					if(($offset+1)*$topResultSize < $count)
					{
						print "<td width=35px><a href=\"?p=top&s=".$sortcat."&o=".$order."&g=".$games."&n=".($offset+1)."\">></a></td>";
					}
					else
					{
						print "<td width=35px><span style=\"color:#ddd;\">></span></td>";
					}
				}
				?>
				</tr>
				</h4>
				</table>
			</td>			
		</tr>
	</table>
</div>
<div class="pageholder" id="pageholder">
	<div id="theader">
		<table class="tableheader" id="tableheader">
			<tr>
				<td class="rowuh" colspan=15>
					<FORM action="" method=POST id=form1 name=form1>
						 Minimum Games Played:
						 <input type="text" id=games name=games value="<?php print $games;?>">
						   
						 <input type="button" id=button name=button onClick="gamesPlayed(document.getElementById('games').value)" value="Update" style="width:80px; color:#ebeb7d;">
					</FORM>
				</td>
			</tr>
			<tr class="headercell">
				<td width=25px>#</td>
<?php
if($offset == 'all')
{
	$sortoffset = $offset;
}
else
{
	$sortoffset = 0;
}

//User Name
if($sortcat == "name")
{
	if($order == "asc")
	{
		print("<td width=175px><a href=\"?p=top&s=name&o=desc&g=".$games."&n=".$sortoffset."\">Name</a></td>");
	}
	else
	{
		print("<td width=175px><a href=\"?p=top&s=name&o=asc&g=".$games."&n=".$sortoffset."\">Name</a></td>");
	}
}
else
{
	print("<td width=175px><a href=\"?p=top&s=name&o=asc&g=".$games."&n=".$sortoffset."\">Name</a></td>");
}
//Score
if($sortcat == "totalscore")
{
	if($order == "asc")
	{
		print("<td width=100px><a href=\"?p=top&s=totalscore&o=desc&g=".$games."&n=".$sortoffset."\">Score</a></td>");
	}
	else
	{
		print("<td width=100px><a href=\"?p=top&s=totalscore&o=asc&g=".$games."&n=".$sortoffset."\">Score</a></td>");
	}
}
else
{
	print("<td width=100px><a href=\"?p=top&s=totalscore&o=desc&g=".$games."&n=".$sortoffset."\">Score</a></td>");
}
//Games
if($sortcat == "totgames")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=totgames&o=desc&g=".$games."&n=".$sortoffset."\">Games</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=totgames&o=asc&g=".$games."&n=".$sortoffset."\">Games</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=totgames&o=desc&g=".$games."&n=".$sortoffset."\">Games</a></td>");
}
//Wins
if($sortcat == "wins")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=wins&o=desc&g=".$games."&n=".$sortoffset."\">Wins</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=wins&o=asc&g=".$games."&n=".$sortoffset."\">Wins</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=wins&o=desc&g=".$games."&n=".$sortoffset."\">Wins</a></td>");
}
//Losses
if($sortcat == "losses")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=losses&o=desc&g=".$games."&n=".$sortoffset."\">Losses</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=losses&o=asc&g=".$games."&n=".$sortoffset."\">Losses</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=losses&o=desc&g=".$games."&n=".$sortoffset."\">Losses</a></td>");
}
//Kills
if($sortcat == "kills")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=kills&o=desc&g=".$games."&n=".$sortoffset."\">Kills</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=kills&o=asc&g=".$games."&n=".$sortoffset."\">Kills</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=kills&o=desc&g=".$games."&n=".$sortoffset."\">Kills</a></td>");
}
//CreepKills
if($sortcat == "deaths")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=deaths&o=desc&g=".$games."&n=".$sortoffset."\">Deaths</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=deaths&o=asc&g=".$games."&n=".$sortoffset."\">Deaths</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=deaths&o=desc&g=".$games."&n=".$sortoffset."\">Deaths</a></td>");
}
//Assists
if($sortcat == "assists")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=assists&o=desc&g=".$games."&n=".$sortoffset."\">Assists</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=assists&o=asc&g=".$games."&n=".$sortoffset."\">Assists</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=assists&o=desc&g=".$games."&n=".$sortoffset."\">Assists</a></td>");
}
//KDRatio
if($sortcat == "killdeathratio")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."&n=".$sortoffset."\">Kills/<br>Death</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=killdeathratio&o=asc&g=".$games."&n=".$sortoffset."\">Kills/<br>Death</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=killdeathratio&o=desc&g=".$games."&n=".$sortoffset."\">Kills/<br>Death</a></td>");
}
//Creep Kills
if($sortcat == "creepkills")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=creepkills&o=desc&g=".$games."&n=".$sortoffset."\">Creep<br>Kills</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=creepkills&o=asc&g=".$games."&n=".$sortoffset."\">Creep<br>Kills</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=creepkills&o=desc&g=".$games."&n=".$sortoffset."\">Creep<br>Kills</a></td>");
}
//Denies
if($sortcat == "creepdenies")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."&n=".$sortoffset."\">Creep<br>Denies</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=creepdenies&o=asc&g=".$games."&n=".$sortoffset."\">Creep<br>Denies</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=creepdenies&o=desc&g=".$games."&n=".$sortoffset."\">Creep<br>Denies</a></td>");
}
//Neutral Kills
if($sortcat == "neutralkills")
{
	if($order == "asc")
	{
		print("<td width=70px><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."&n=".$sortoffset."\">Neutral<br>Kills</a></td>");
	}
	else
	{
		print("<td width=70px><a href=\"?p=top&s=neutralkills&o=asc&g=".$games."&n=".$sortoffset."\">Neutral<br>Kills</a></td>");
	}
}
else
{
	print("<td width=70px><a href=\"?p=top&s=neutralkills&o=desc&g=".$games."&n=".$sortoffset."\">Neutral<br>Kills</a></td>");
}

?>
				<td width=16px></td>
			</tr>
		</table>
	</div>
	<div id="datawrapper">
		<table class="table" id="data">
<?php
if($scoreFromDB)        //Using score table
{
$sql = "select gp.name as name, bans.name as banname, avg(dp.courierkills) as courierkills, avg(dp.raxkills) as raxkills,
avg(dp.towerkills) as towerkills, avg(dp.assists) as assists, avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills,
avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills, sc.score as totalscore, count(*) as totgames, 
SUM(case when(((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= $minPlayedRatio) then 1 else 0 end) as wins, 
SUM(case when(((dg.winner = 2 and dp.newcolour < 6) or (dg.winner = 1 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= $minPlayedRatio) then 1 else 0 end) as losses
from gameplayers as gp 
LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid 
LEFT JOIN dotaplayers as dp ON dg.gameid = dp.gameid and gp.colour = dp.colour and dp.newcolour <> 12 and dp.newcolour <> 6
LEFT JOIN games as ga ON dp.gameid = ga.id 
LEFT JOIN scores as sc ON sc.name = gp.name 
LEFT JOIN bans on bans.name = gp.name
where dg.winner <> 0 ";
}
else                    //Using score formula
{
$sql = "select *, ($scoreFormula) as totalscore from (
select gp.name as name, bans.name as banname, avg(dp.courierkills) as courierkills, avg(dp.assists) as assists,
avg(dp.creepdenies) as creepdenies, avg(dp.creepkills) as creepkills, avg(dp.towerkills) as towerkills, avg(dp.raxkills) as raxkills,
avg(dp.neutralkills) as neutralkills, avg(dp.deaths) as deaths, avg(dp.kills) as kills,
count(1) as totgames, 
SUM(case when(((dg.winner = 1 and dp.newcolour < 6) or (dg.winner = 2 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= $minPlayedRatio) then 1 else 0 end) as wins, 
SUM(case when(((dg.winner = 2 and dp.newcolour < 6) or (dg.winner = 1 and dp.newcolour > 6)) AND gp.`left`/ga.duration >= $minPlayedRatio) then 1 else 0 end) as losses
from gameplayers as gp 
LEFT JOIN dotagames as dg ON gp.gameid = dg.gameid 
LEFT JOIN dotaplayers as dp ON dg.gameid = dp.gameid and gp.colour = dp.colour and dp.newcolour <> 12 and dp.newcolour <> 6
LEFT JOIN games as ga ON dp.gameid = ga.id 
LEFT JOIN bans on bans.name = gp.name
where dg.winner <> 0 ";
}

if($ignorePubs)
{
$sql = $sql." and gamestate = '17'";
}
else if($ignorePrivs)
{
$sql = $sql." and gamestate = '16'";
}

$sql = $sql." group by gp.name having totgames >= $games";
if(!$scoreFromDB)
{
	$sql = $sql.") as i";
}
$sql = $sql." ORDER BY $sortcat $order, name asc";



if($offset!='all')
{
$sql = $sql." LIMIT ".$topResultSize*$offset.", $topResultSize";
}

if($dbType == 'sqlite')
{
	$rank = 1;
	foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
	{
		$name=$row["name"];
        $banname=$row["banname"];
		$totgames=$row["totgames"];
		$kills=$row["kills"];
		$death=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$courierkills=$row["courierkills"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		$totalscore=$row["totalscore"];
		$killdeathratio=getRatio($kills, $death);

	?>
	<tr class="row">
	<td width=25px><?php print $rank; ?></td>
	<td width=175px><a <?php if($banname<>'') { print 'style="color:#e56879"'; } ?> href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>
	<td width=100px><?php print ROUND($totalscore,2); ?></td>
	<td width=70px><?php print $totgames;?></td>
	<td width=70px><?php print $wins; ?></td>
	<td width=70px><?php print $losses; ?></td>
	<td width=70px><?php print ROUND($kills,1); ?></td>
	<td width=70px><?php print ROUND($death,1); ?></td>
	<td width=70px><?php print ROUND($assists,1); ?></td>
	<td width=70px><?php print $killdeathratio; ?></td>

	<td width=70px><?php print ROUND($creepkills,1) ?></td>
	<td width=70px><?php print ROUND($creepdenies,1); ?></td>
	<td width=70px><?php print ROUND($neutralkills,1); ?></td>


	</tr>

	<?php
	$rank = $rank + 1;
	}
}
else
{
	$rank = 1;
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {

		$name=$row["name"];
		$banname=$row["banname"];
		$totgames=$row["totgames"];
		$kills=$row["kills"];
		$death=$row["deaths"];
		$assists=$row["assists"];
		$creepkills=$row["creepkills"];
		$creepdenies=$row["creepdenies"];
		$neutralkills=$row["neutralkills"];
		$courierkills=$row["courierkills"];
		$wins=$row["wins"];
		$losses=$row["losses"];
		$totalscore=$row["totalscore"];
		$killdeathratio=getRatio($kills, $death);

	?>
	<tr class="row">
	<td width=25px><?php print $rank; ?></td>
        <td width=175px><a <?php if($banname<>'') { print 'style="color:#e56879"'; } ?> href="?p=user&u=<?php print $name; ?>&s=datetime&o=desc&n=<?php if($displayStyle=='all'){ print 'all'; } else { print '0'; } ?>"><?php print $name; ?></a></td>
	<td width=100px><?php print ROUND($totalscore,2); ?></td>
	<td width=70px><?php print $totgames;?></td>
	<td width=70px><?php print $wins; ?></td>
	<td width=70px><?php print $losses; ?></td>
	<td width=70px><?php print ROUND($kills,1); ?></td>
	<td width=70px><?php print ROUND($death,1); ?></td>
	<td width=70px><?php print ROUND($assists,1); ?></td>
	<td width=70px><?php print $killdeathratio; ?></td>

	<td width=70px><?php print ROUND($creepkills,1) ?></td>
	<td width=70px><?php print ROUND($creepdenies,1); ?></td>
	<td width=70px><?php print ROUND($neutralkills,1); ?></td>


	</tr>

	<?php
		$rank = $rank + 1;
	}
	mysql_free_result($result);
}
?>
</table>
</div>
</div>
<div id="footerdata" class="footerdata">
	<table class="table" width=1016px>
		<tr>
			<td colspan=5>
				<h3>All Time Top Statistics per Game</h3>
			</td>
		</tr>
		<tr>


<?php

$arrStatRow = array(
	"Top Kills" => "SELECT original as topHero, description as topHeroName, kills as topValue, name as topUser, a.gameid as topGame
		FROM dotaplayers AS a 
		LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
		LEFT JOIN games as c on a.gameid = c.id
		LEFT JOIN heroes as d on hero = heroid",
	"Top Assists" => "SELECT original as topHero, description as topHeroName, assists as topValue, name as topUser, a.gameid as topGame
		FROM dotaplayers AS a 
		LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
		LEFT JOIN games as c on a.gameid = c.id
		LEFT JOIN heroes as d on hero = heroid",
	"Top Deaths" => "SELECT original as topHero, description as topHeroName, deaths as topValue, name as topUser, a.gameid as topGame
		FROM dotaplayers AS a 
		LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
		LEFT JOIN games as c on a.gameid = c.id
		LEFT JOIN heroes as d on hero = heroid",
	"Top Creep Kills" => "SELECT original as topHero, description as topHeroName, creepkills as topValue, name as topUser, a.gameid as topGame
		FROM dotaplayers AS a 
		LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
		LEFT JOIN games as c on a.gameid = c.id
		LEFT JOIN heroes as d on hero = heroid",
	"Top Creep Denies" => "SELECT original as topHero, description as topHeroName, creepdenies as topValue, name as topUser, a.gameid as topGame
		FROM dotaplayers AS a 
		LEFT JOIN gameplayers AS b ON b.gameid = a.gameid and a.colour = b.colour 
		LEFT JOIN games as c on a.gameid = c.id
		LEFT JOIN heroes as d on hero = heroid");

if($dbType == 'sqlite') // #################################################### SQLITE #########################################################
{
	foreach($arrStatRow as $title => $sql)
	{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="footerheadercell"><?php print $title; ?></td>
					</tr>

<?php

		if($ignorePubs)
		{
			$sql = $sql." WHERE gamestate = '17'";
		}
		else if($ignorePrivs)
		{
			$sql = $sql." WHERE gamestate = '16'";
		}
		$sql = $sql." ORDER BY topValue DESC, a.id ASC LIMIT ".$monthlyTopsListSize;

		foreach ($dbHandle->query($sql, PDO::FETCH_ASSOC) as $row)
		{
			printStatsRowType($row);
		}
?>
				</table>
			</td>
			
<?php
	}
}
else  // #################################################### MYSQL #########################################################
{
	foreach($arrStatRow as $title => $sql)
	{
?>

			<td width=20%>
				<table width=100%>
					<tr>
						<td align=center colspan=3 class="footerheadercell"><?php print $title; ?></td>
					</tr>

<?php
		if($ignorePubs)
		{
			$sql = $sql." WHERE gamestate = '17'";
		}
		else if($ignorePrivs)
		{
			$sql = $sql." WHERE gamestate = '16'";
		}
		$sql = $sql." ORDER BY topValue DESC, a.id ASC LIMIT ".$monthlyTopsListSize;

		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) 
		{
			printStatsRowType($row);
		}
		mysql_free_result($result);
?>

				</table>
			</td>
			
<?php
	}
}
?>			

		</tr>
	</table>
</div>
<div id="footer" class="footer">
		<h5>Total Players: <?php print $count; ?></h5>
</div>
