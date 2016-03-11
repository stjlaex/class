<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

<!--  -->

<xsl:output method='html' />


<xsl:template match="students">
  <xsl:variable name="bids" select="set:distinct(student/assessments/assessment/subject/value)" />
  <xsl:variable name="courses" select="set:distinct(student/assessments/assessment/course/value)" />
  <xsl:variable name="labels" select="set:distinct(asstable/ass/label)" />
  <xsl:variable name="dates" select="set:distinct(asstable/ass/date)" />
  <xsl:variable name="stepno" select="round(count($dates) -1 )" />


  <div class="head">
	<div class="left">
	  <label>Assessment Comparison: </label>
	  <label><xsl:value-of select="$courses[1]" /></label>
	  <xsl:for-each select="$dates">
		<label>
		  <xsl:value-of select="." />
		</label>
	  </xsl:for-each>
	</div>
	<div class="right">
	  <xsl:value-of select="description/value" />
	</div>
  </div>
  <div class="spacer"></div>

  <div>
	<table>
	  <tr>
		<th colspan="3"><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="bids" select="$bids"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="student">
		<xsl:with-param name="bids" select="$bids"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
		<xsl:with-param name="stepno" select="$stepno"/>
	  </xsl:apply-templates>

	  <tr>
		<th colspan="3"><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="bids" select="$bids"/>
		</xsl:call-template>
	  </tr>

	  <tr>
		<td colspan="3">
		  <table class="subcell">
			<xsl:for-each select="$dates">
			  <xsl:sort select="position()" data-type="number" order="ascending"/>
			  <tr>
				<td>
				  <xsl:value-of select="." />
				</td>
			  </tr>
			</xsl:for-each>
		  </table>
		</td>
		<xsl:call-template name="averagecolumn">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="labels" select="$labels"/>
		  <xsl:with-param name="dates" select="$dates"/>
		</xsl:call-template>	
	  </tr>

	</table>
  </div>


<div class='spacer'></div>
<hr />
</xsl:template>

<xsl:template match="student">
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="stepno"></xsl:param>
	  <tr>
		<th>
			<xsl:call-template name="averagerow">
			  <xsl:with-param name="labels" select="$labels"/>
			  <xsl:with-param name="dates" select="$dates"/>
			  <xsl:with-param name="stepno" select="$stepno"/>
			</xsl:call-template>
		</th>
		<th>
			<xsl:value-of select="displayfullsurname/value/text()" />
		</th>
		<th>
			<xsl:value-of select="registrationgroup/value/text()" />
		</th>

		<xsl:call-template name="subjectcell">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="labels" select="$labels"/>
		  <xsl:with-param name="dates" select="$dates"/>
		  <xsl:with-param name="stepno" select="$stepno"/>
		</xsl:call-template>

	  </tr>

</xsl:template>


<xsl:template name="subjectcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="stepno"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">
    <xsl:variable name="pids" select="set:distinct(../student/assessments/assessment[subject/value=$bids[$index]]/subjectcomponent/value)" />

    <xsl:call-template name="componentcell">
      <xsl:with-param name="index" select="1"/>
      <xsl:with-param name="bid" select="$bids[$index]"/>
      <xsl:with-param name="pids" select="$pids"/>
      <xsl:with-param name="labels" select="$labels"/>
      <xsl:with-param name="dates" select="$dates"/>
      <xsl:with-param name="stepno" select="$stepno"/>
    </xsl:call-template>

    <xsl:call-template name="subjectcell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	  <xsl:with-param name="stepno" select="$stepno"/>
	</xsl:call-template>
  </xsl:if>
</xsl:template>


<xsl:template name="componentcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="stepno"></xsl:param>

  <xsl:variable name="maxindex" select="count($pids)" />

  <xsl:if test="$maxindex=0">
	<td><xsl:text>&#160; </xsl:text>
	</td>
  </xsl:if>

  <xsl:if test="$index &lt; $maxindex+1">
	<td><xsl:text>&#160; </xsl:text>
	  <table class="subcell">	
		<xsl:call-template name="assrow">
		  <xsl:with-param name="step" select="1"/>
		  <xsl:with-param name="bid" select="$bid"/>
		  <xsl:with-param name="pid" select="$pids[$index]"/>
		  <xsl:with-param name="labels" select="$labels"/>
		  <xsl:with-param name="dates" select="$dates"/>
		  <xsl:with-param name="stepno" select="$stepno"/>
		</xsl:call-template>
	  </table>
	</td>

	<xsl:call-template name="componentcell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="pids" select="$pids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	  <xsl:with-param name="stepno" select="$stepno"/>
	</xsl:call-template>
  </xsl:if>


</xsl:template>



<xsl:template name="assrow">
  <xsl:param name="step">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pid"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="stepno"></xsl:param>
  
  <xsl:variable name="one" select="$step" />
  <xsl:variable name="two" select="$step + 1 " />

	<tr>
	  <!-- date order is descending here for this to be correct -->
		<xsl:call-template name="asscell">
		  <xsl:with-param name="bid" select="$bid"/>
		  <xsl:with-param name="pid" select="$pid"/>
		  <xsl:with-param name="id_db1" select="../asstable/ass[date=$dates[$two]]/id_db"/>
		  <xsl:with-param name="id_db2" select="../asstable/ass[date=$dates[$one]]/id_db"/>
		</xsl:call-template>
	</tr>

  <xsl:if test="$step &lt; $stepno">
	  <xsl:call-template name="assrow">
		<xsl:with-param name="step" select="$step+1"/>
		<xsl:with-param name="bid" select="$bid"/>
		<xsl:with-param name="pid" select="$pid"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
		<xsl:with-param name="stepno" select="$stepno"/>
	  </xsl:call-template>
  </xsl:if>

</xsl:template>




<xsl:template name="asscell">
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pid"></xsl:param>
  <xsl:param name="id_db1"></xsl:param>
  <xsl:param name="id_db2"></xsl:param>
  <xsl:variable name="val1" select="assessments/assessment[id_db=$id_db1][subject/value=$bid][subjectcomponent/value=$pid]/value/value" />
  <xsl:variable name="val2" select="assessments/assessment[id_db=$id_db2][subject/value=$bid][subjectcomponent/value=$pid]/value/value" />
  <xsl:variable name="diff" select="$val2 - $val1" />


  <xsl:variable name="totalcount1" select="count(../student/assessments/assessment[id_db=$id_db1][subject/value=$bid][subjectcomponent/value=$pid]/value/value)" />
  <xsl:variable name="totalval1" select="round(sum(../student/assessments/assessment[id_db=$id_db1][subject/value=$bid][subjectcomponent/value=$pid]/value/value) div $totalcount1)" />
  <xsl:variable name="totalcount2" select="count(../student/assessments/assessment[id_db=$id_db2][subject/value=$bid][subjectcomponent/value=$pid]/value/value)" />
  <xsl:variable name="totalval2" select="round(sum(../student/assessments/assessment[id_db=$id_db2][subject/value=$bid][subjectcomponent/value=$pid]/value/value) div $totalcount2)" />
  <xsl:variable name="totaldiff" select="$totalval2 - $totalval1" />
  <xsl:variable name="relativediff" select="$diff - $totaldiff" />

  <td>
	<xsl:value-of select="assessments/assessment[id_db=$id_db1][subject/value=$bid][subjectcomponent/value=$pid]/result/value" />
	<xsl:text>&#160; </xsl:text>
  </td>
  <td>
	<xsl:value-of select="assessments/assessment[id_db=$id_db2][subject/value=$bid][subjectcomponent/value=$pid]/result/value" />
	<xsl:text>&#160; </xsl:text>
  </td>

  <td>
	<xsl:text>&#160; </xsl:text>
	  <xsl:choose>
		<xsl:when test="$relativediff &lt; 0">
		  <span class="lolite">
			<xsl:value-of select="$diff" />
		  </span>
		</xsl:when>
		<xsl:when test="$relativediff &gt; 0">
		  <span class="golite">
			<xsl:value-of select="$diff" />
		  </span>
		</xsl:when>
		<xsl:when test="$relativediff = 0">
		  <span>
			<xsl:value-of select="$diff" />
		  </span>
		</xsl:when>
		<xsl:otherwise>
		  <xsl:text>&#160; </xsl:text>
		</xsl:otherwise>
	  </xsl:choose>
  </td>

</xsl:template>

<xsl:template name="headerrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"> </xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">
    <xsl:variable name="pids" select="set:distinct(student/assessments/assessment[subject/value=$bids[$index]]/subjectcomponent/value)" />

	  <xsl:choose>
		<xsl:when test="count($pids) &gt; 1">
		<xsl:for-each select="$pids">
		  <th>
		    <xsl:value-of select="." />
		  </th>
		</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
		  <th>
		    <xsl:value-of select="$bids[$index]" />
		  </th>
		</xsl:otherwise>
	  </xsl:choose>

  
	<xsl:call-template name="headerrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="averagerow">
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>
  <xsl:param name="stepno"></xsl:param>

  <xsl:variable name="one" select="1" />
  <xsl:variable name="two" select="$stepno + 1 " />

  <xsl:call-template name="averagecell">
	<!-- date order is descending here for this to be correct -->
	<xsl:with-param name="id_db1" select="../asstable/ass[date=$dates[$two]]/id_db"/>
	<xsl:with-param name="id_db2" select="../asstable/ass[date=$dates[$one]]/id_db"/>
  </xsl:call-template>

</xsl:template>


<xsl:template name="averagecell">
  <xsl:param name="id_db1"></xsl:param>
  <xsl:param name="id_db2"></xsl:param>

  <xsl:variable name="asscount1" select="count(assessments/assessment[id_db=$id_db1]/value/value)" />
  <xsl:variable name="asscount2" select="count(assessments/assessment[id_db=$id_db2]/value/value)" />
  <xsl:variable name="sum1" select="sum(assessments/assessment[id_db=$id_db1]/value/value)" />
  <xsl:variable name="sum2" select="sum(assessments/assessment[id_db=$id_db2]/value/value)" />

  <xsl:if test="$asscount1 = $asscount2">
	<xsl:variable name="diff" select="round( $sum2 div $asscount2 ) - round( $sum1 div $asscount1 )" />
	<xsl:text>&#160; </xsl:text>
	  <xsl:choose>
		<xsl:when test="$diff &lt; 0">
		  <span class="lolife">
			<xsl:text>&#160;</xsl:text>
		  </span>
		</xsl:when>
		<xsl:when test="$diff &gt; 0">
		  <span class="golife">
			<xsl:text>&#160;</xsl:text>
		  </span>
		</xsl:when>
		<xsl:when test="$diff = 0">
		  <span class="pauselife">
			<xsl:text>&#160;</xsl:text>
		  </span>
		</xsl:when>
		<xsl:otherwise>
			<xsl:text>&#160;</xsl:text>
		</xsl:otherwise>
	  </xsl:choose>
	</xsl:if>

</xsl:template>




<xsl:template name="averagecolumn">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">
    <xsl:variable name="pids" select="set:distinct(student/assessments/assessment[subject/value=$bids[$index]]/subjectcomponent/value)" />

	<xsl:call-template name="averagecomponent">
	  <xsl:with-param name="index" select="1"/>
	  <xsl:with-param name="bid" select="$bids[$index]"/>
	  <xsl:with-param name="pids" select="$pids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>
  
    <xsl:call-template name="averagecolumn">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>


<xsl:template name="averagecomponent">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($pids)" />
  <xsl:if test="$index &lt; $maxindex+1">

	<td>
	  <table class="subcell">
	  <xsl:call-template name="averagesubrow">
		<xsl:with-param name="index" select="1"/>
		<xsl:with-param name="bid" select="$bid"/>
		<xsl:with-param name="pid" select="$pids[$index]"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:call-template>
	  </table>
	</td>
  

    <xsl:call-template name="averagecomponent">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="pids" select="$pids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>




<xsl:template name="averagesubrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pid"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($dates)" />
  <xsl:if test="$index &lt; $maxindex+1">

	<tr>
		<xsl:call-template name="averagesubcell">
		  <xsl:with-param name="bid" select="$bid"/>
		  <xsl:with-param name="pid" select="$pid"/>
		  <xsl:with-param name="id_db" select="asstable/ass[date=$dates[$index]]/id_db"/>
		</xsl:call-template>
	</tr>

    <xsl:call-template name="averagesubrow">
	  <xsl:with-param name="index" select="$index + 1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="pid" select="$pid"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="averagesubcell">
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pid"></xsl:param>
  <xsl:param name="id_db"></xsl:param>

  <xsl:variable name="asscount" select="count(student/assessments/assessment[id_db=$id_db][subject/value=$bid][subjectcomponent/value=$pid]/value/value)" />

	  <xsl:choose>
		<xsl:when test="$asscount &gt; 0">
		  <td>
			<xsl:variable name="value" select="round(sum(student/assessments/assessment[id_db=$id_db][subject/value=$bid][subjectcomponent/value=$pid]/value/value) div $asscount)" />
			<xsl:value-of select="student/assessments/assessment[value/value=$value]/result/value" />


			<xsl:if test="asstable/ass[id_db=$id_db]/bands[name='A']/value!=' '">
			<xsl:variable name="banda" select="asstable/ass[id_db=$id_db]/bands[name='A']/value" />
			<xsl:variable name="bandb" select="asstable/ass[id_db=$id_db]/bands[name='B']/value" />
			<xsl:variable name="bandc" select="asstable/ass[id_db=$id_db]/bands[name='C']/value" />
			<xsl:element name="div">
			  <xsl:choose>
				<xsl:when test="$asscount &gt; 0">
				  <xsl:value-of select="restable/res[value=$bandc]/label" />
				  <xsl:text>:</xsl:text>
				  <xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subject/value=$bid][subjectcomponent/value=$pid]/value[value &gt; $bandc]) div $asscount,'##%')" />
				  <xsl:text> </xsl:text>
				  <xsl:value-of select="restable/res[value=$bandb]/label" />
				  <xsl:text>:</xsl:text>
				  <xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subject/value=$bid][subjectcomponent/value=$pid]/value[value &gt; $bandb]) div $asscount,'##%')" />
				  <xsl:text> </xsl:text>
				  <xsl:value-of select="restable/res[value=$banda]/label" />
				  <xsl:text>:</xsl:text>
				  <xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subject/value=$bid][subjectcomponent/value=$pid]/value[value &gt; $banda]) div $asscount,'##%')" />
				</xsl:when>
				<xsl:otherwise>
				  <xsl:text> </xsl:text>
				</xsl:otherwise>
			  </xsl:choose>
			</xsl:element>
			</xsl:if>

		  </td>
		</xsl:when>
		<xsl:otherwise>
		  <td>
			<xsl:text>&#160; </xsl:text>
		  </td>
		</xsl:otherwise>
	  </xsl:choose>

</xsl:template>



</xsl:stylesheet>
