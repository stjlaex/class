<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

<!--  -->

<xsl:output method='html' />


<xsl:template match="students">
  <xsl:variable name="bids" select="set:distinct(student/assessments/assessment/subject/value)" />

  <xsl:variable name="courses" select="set:distinct(student/assessments/assessment/course/value)" />
  <xsl:variable name="labels" select="set:distinct(asstable/ass/label)" />
  <xsl:variable name="dates" select="set:distinct(asstable/ass/date)" />

  <div class="head">
	<div class="left">
	  <label>Assessment Sheet: </label>
	  <label><xsl:value-of select="$courses[1]" /></label>
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
		<xsl:for-each select="$bids">
		  <th>
			<xsl:value-of select="." />
		  </th>
		</xsl:for-each>
		<th><label>A*&#160;/&#160;A</label></th>
		<th><label>A*&#160;-&#160;C</label></th>
	  </tr>

	  <xsl:apply-templates select="student">
		<xsl:with-param name="bids" select="$bids"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:apply-templates>

	  <tr>
		<th colspan="3"><label></label></th>
		<xsl:for-each select="$bids">
		  <th>
			<xsl:value-of select="." />
		  </th>
		</xsl:for-each>
		<th colspan="2"><label></label></th>
	  </tr>

	  <tr>
		<td colspan="3">
		  <table class="subcell">
			<xsl:for-each select="$dates">
			  <xsl:sort select="position()" data-type="number" order="descending"/>
			  <tr>
				<td>
				  <xsl:value-of select="." />
				</td>
			  </tr>
			</xsl:for-each>
		  </table>
		</td>

		<xsl:call-template name="averagesubject">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="labels" select="$labels"/>
		  <xsl:with-param name="dates" select="$dates"/>
		</xsl:call-template>
	
		<th><label>A* / A</label></th>
		<th><label>A* - C</label></th>
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
	  <tr>
		<th>
			<xsl:value-of select="position()" />
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
		</xsl:call-template>


		<td colspan="2">
		  <table class="subcell">
			<xsl:call-template name="averagerow">
			  <xsl:with-param name="index" select="count($dates)"/>
			  <xsl:with-param name="labels" select="$labels"/>
			  <xsl:with-param name="dates" select="$dates"/>
			</xsl:call-template>
		  </table>
		</td>

	  </tr>
</xsl:template>


<xsl:template name="subjectcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<td>
	  <table class="subcell">
	  <xsl:call-template name="assrow">
		<xsl:with-param name="index" select="count($dates)"/>
		<xsl:with-param name="bid" select="$bids[$index]"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:call-template>
	  </table>
	</td>
  
    <xsl:call-template name="subjectcell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="assrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>


  <xsl:if test="$index &gt; 0">

	<tr>

	  <xsl:choose>
		<xsl:when test="$index &lt; count($dates)">
		  <xsl:call-template name="asscell">
			<xsl:with-param name="bid" select="$bid"/>
			<xsl:with-param name="id_db" select="../asstable/ass[date=$dates[$index]]/id_db"/>
			<xsl:with-param name="previous_id_db" select="../asstable/ass[date=$dates[$index+1]]/id_db"/>
		  </xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
		  <xsl:call-template name="asscell">
			<xsl:with-param name="bid" select="$bid"/>
			<xsl:with-param name="id_db" select="../asstable/ass[date=$dates[$index]]/id_db"/>
			<xsl:with-param name="previous_id_db" select="-1"/>
		  </xsl:call-template>
		</xsl:otherwise>
	  </xsl:choose>
		  
	</tr>

    <xsl:call-template name="assrow">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>




<xsl:template name="asscell">
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="id_db"></xsl:param>
  <xsl:param name="previous_id_db"></xsl:param>

  <xsl:variable name="previous_value" select="sum(assessments/assessment[id_db=$previous_id_db][subject/value=$bid]/value/value)" />
  <xsl:variable name="current_value" select="sum(assessments/assessment[id_db=$id_db][subject/value=$bid]/value/value)" />

  <xsl:variable name="diff">
  <xsl:choose>
	<xsl:when test="count(assessments/assessment[id_db=$previous_id_db][subject/value=$bid]/value/value)=count(assessments/assessment[id_db=$id_db][subject/value=$bid]/value/value)">
	  <xsl:value-of select="round($current_value - $previous_value)" />
	</xsl:when>
	<xsl:otherwise>
	</xsl:otherwise>
  </xsl:choose>
  </xsl:variable>

  <td>
	<xsl:for-each select="assessments/assessment[id_db=$id_db][subject/value=$bid]">
	  <xsl:choose>
		<xsl:when test="value/value &lt; 5">
		  <span class="lolite">
			<xsl:value-of select="result/value" />
		  </span>
		</xsl:when>
		<xsl:otherwise>
		  <xsl:value-of select="result/value" />
		</xsl:otherwise>
	  </xsl:choose>
	</xsl:for-each>
  </td>
  <td>
	<xsl:if test="$current_value>0 and $previous_value>0 and $previous_id_db!=-1">
	  <xsl:choose>
		<xsl:when test="$diff &lt; 0">
		  <span class="lolite">
			<xsl:value-of select="$diff" />
		  </span>
		</xsl:when>
		<xsl:when test="$diff &gt; 0">
		  <span class="golite">
		  <xsl:value-of select="$diff" />
		  </span>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="$diff" />
		</xsl:otherwise>
	  </xsl:choose>
	  </xsl:if>
  </td>

</xsl:template>



<xsl:template name="averagerow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:if test="$index &gt; 0">

	<tr>
	  <xsl:call-template name="averagecell">
		<xsl:with-param name="id_db" select="../asstable/ass[date=$dates[$index]]/id_db"/>
	  </xsl:call-template>
	</tr>

    <xsl:call-template name="averagerow">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="averagecell">
  <xsl:param name="id_db"></xsl:param>

  <xsl:variable name="asscount" select="count(assessments/assessment[id_db=$id_db]/value/value)" />

  <td>
	<xsl:text>&#160; </xsl:text>
	<xsl:if test="$asscount &gt; 0">
	  <xsl:value-of select="count(assessments/assessment[id_db=$id_db]/value[value &gt; 6])" />
	</xsl:if>
  </td>
  <td>
	<xsl:text>&#160; </xsl:text>
	<xsl:if test="$asscount &gt; 0">
	  <xsl:value-of select="count(assessments/assessment[id_db=$id_db]/value[value &gt; 4])" />
	</xsl:if>
  </td>
</xsl:template>




<xsl:template name="averagesubject">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<td>
	  <table class="subcell">
	  <xsl:call-template name="averagesubrow">
		<xsl:with-param name="index" select="count($dates)"/>
		<xsl:with-param name="bid" select="$bids[$index]"/>
		<xsl:with-param name="labels" select="$labels"/>
		<xsl:with-param name="dates" select="$dates"/>
	  </xsl:call-template>
	  </table>
	</td>

    <xsl:call-template name="averagesubject">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>

<xsl:template name="averagesubrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="labels"></xsl:param>
  <xsl:param name="dates"></xsl:param>

  <xsl:if test="$index &gt; 0">

	<tr>
		<xsl:call-template name="averagesubcell">
		  <xsl:with-param name="bid" select="$bid"/>
		  <xsl:with-param name="id_db" select="asstable/ass[date=$dates[$index]]/id_db"/>
		</xsl:call-template>
	</tr>

    <xsl:call-template name="averagesubrow">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="labels" select="$labels"/>
	  <xsl:with-param name="dates" select="$dates"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>

<xsl:template name="averagesubcell">
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="id_db"></xsl:param>

  <xsl:variable name="asscount" select="count(student/assessments/assessment[id_db=$id_db][subject/value=$bid]/value/value)" />

	  <xsl:choose>
		<xsl:when test="$asscount &gt; 0">
		  <td>
			<xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subject/value=$bid]/value[value &gt; 6]) div $asscount,'##%')" />
		  </td>
		  <td>
			<xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subject/value=$bid]/value[value &gt; 4]) div $asscount,'##%')" />
		  </td>
		</xsl:when>
		<xsl:otherwise>
		  <td>
			<xsl:text>&#160; </xsl:text>
		  </td>
		  <td>
			<xsl:text>&#160; </xsl:text>
		  </td>
		</xsl:otherwise>
	  </xsl:choose>

</xsl:template>



</xsl:stylesheet>
