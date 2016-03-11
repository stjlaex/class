<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">


<xsl:output method='html' />


<xsl:template match="students">
  <xsl:variable name="asses" select="asstable/ass" />
  <xsl:variable name="bids" select="set:distinct(student/assessments/assessment/subject/value)" />
  <xsl:variable name="courses" select="set:distinct(student/assessments/assessment/course/value)" />
  <xsl:variable name="maxindex" select="count($asses)" />

  <div class="head">
	  <label>Summary Assessment Sheet: </label>
	  <label><xsl:value-of select="$courses[1]" /></label>
	  <label><xsl:value-of select="description/value" /></label>
  </div>

  <div>
	<table>
	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="$maxindex"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="asses" select="$asses"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="restable/res">
		<xsl:sort select="value" order="descending" data-type="number" />
		<xsl:with-param name="bids" select="$bids"/>
		<xsl:with-param name="asses" select="$asses"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="$maxindex"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="asses" select="$asses"/>
		</xsl:call-template>
	  </tr>
	</table>
  </div>

<div class='spacer'></div>
<hr />
<div class='spacer'></div>
</xsl:template>





<xsl:template match="restable/res">
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="asses"></xsl:param>
	  <tr>
		<th>
			<xsl:value-of select="label" />
		</th>

		<xsl:variable name="maxindex" select="count($asses)" />

		<xsl:call-template name="subjectrow">
		  <xsl:with-param name="index" select="$maxindex"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="asses" select="$asses"/>
		  <xsl:with-param name="label" select="label/text()"/>
		</xsl:call-template>

	  </tr>
</xsl:template>


<xsl:template name="subjectrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"> </xsl:param>
  <xsl:param name="asses"> </xsl:param>
  <xsl:param name="label"></xsl:param>
  
  <xsl:if test="$index &gt; 0">

	<xsl:call-template name="subjectcell">
	  <xsl:with-param name="index" select="1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="ass" select="$asses[$index]"/>
	  <xsl:with-param name="label" select="$label"/>
	</xsl:call-template>

	<xsl:call-template name="subjectrow">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="asses" select="$asses"/>
	  <xsl:with-param name="label" select="$label"/>
	</xsl:call-template>

<!--
-->
  </xsl:if>

</xsl:template>





<xsl:template name="subjectcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="ass"></xsl:param>
  <xsl:param name="label"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">
    <xsl:variable name="pids" select="set:distinct(../../student/assessments/assessment[subject/value=string($bids[$index])]/subjectcomponent/value)" />
	<xsl:call-template name="componentcell">
	  <xsl:with-param name="index" select="1"/>
	  <xsl:with-param name="bid" select="$bids[$index]"/>
	  <xsl:with-param name="pids" select="$pids"/>
	  <xsl:with-param name="ass" select="$ass"/>
	  <xsl:with-param name="label" select="$label"/>
	</xsl:call-template>
  
    <xsl:call-template name="subjectcell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="ass" select="$ass"/>
	  <xsl:with-param name="label" select="$label"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="componentcell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pids"></xsl:param>
  <xsl:param name="ass"></xsl:param>
  <xsl:param name="label"></xsl:param>

  <xsl:variable name="maxindex" select="count($pids)" />

  <xsl:if test="$index &lt; $maxindex+1">

  <xsl:if test="count(../../student/assessments/assessment[id_db=string($ass/id_db)][subject/value=string($bid)][subjectcomponent/value=string($pids[$index])]/result/value)>0">
	<td>
	  <table class="subcell">
	  <xsl:call-template name="average">
		<xsl:with-param name="bid" select="$bid"/>
		<xsl:with-param name="pid" select="$pids[$index]"/>
		<xsl:with-param name="ass" select="$ass"/>
		<xsl:with-param name="label" select="$label"/>
	  </xsl:call-template>
	  </table>
	</td>
  </xsl:if>

	<xsl:call-template name="componentcell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="pids" select="$pids"/>
	  <xsl:with-param name="ass" select="$ass"/>
	  <xsl:with-param name="label" select="$label"/>
	</xsl:call-template>
  </xsl:if>


</xsl:template>





<xsl:template name="average">
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="pid"></xsl:param>
  <xsl:param name="ass"></xsl:param>
  <xsl:param name="label"></xsl:param>

  <xsl:variable name="assid" select="$ass/id_db"/>
  <xsl:variable name="totcount" select="count(../../student/assessments/assessment[id_db=string($assid)][subject/value=string($bid)][subjectcomponent/value=string($pid)]/result/value)" />
  <xsl:variable name="valcount" select="count(../../student/assessments/assessment[id_db=string($assid)][subject/value=string($bid)][subjectcomponent/value=string($pid)]/result[value=string($label)])" />

  <td>
	<xsl:if test="$totcount &gt; 0 and $valcount &gt; 0">
	  <xsl:variable name="percent" select="$valcount div $totcount" />
		<xsl:attribute name="class"> 
		  <xsl:choose>
            <xsl:when test="$percent &gt; 0.45">hilife</xsl:when> 
			<xsl:when test="$percent &gt; 0.30">golife</xsl:when> 
			<xsl:when test="$percent &gt; 0.15">pauselife</xsl:when> 
            <xsl:otherwise>nolife</xsl:otherwise> 
		  </xsl:choose> 
		</xsl:attribute>
		<xsl:text>&#160; </xsl:text>
	  <xsl:value-of select="format-number($percent,'## %')" />
	</xsl:if>
  </td>
</xsl:template>




<xsl:template name="headerrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"> </xsl:param>
  <xsl:param name="asses"> </xsl:param>

  <xsl:if test="$index &gt; 0">
  

	<xsl:call-template name="headercell">
	  <xsl:with-param name="index" select="1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="ass" select="$asses[$index]"/>
	</xsl:call-template>

	<xsl:call-template name="headerrow">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="asses" select="$asses"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>




<xsl:template name="headercell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="bids"> </xsl:param>
  <xsl:param name="ass"> </xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />
  <xsl:variable name="assid" select="$ass/id_db"/>

  <xsl:if test="$index &lt; $maxindex+1">
    <xsl:variable name="pids" select="set:distinct(student/assessments/assessment[subject/value=string($bids[$index])]/subjectcomponent/value)" />

  <xsl:if test="count(student/assessments/assessment[id_db=string($assid)][subject/value=string($bids[$index])]/result/value)>0">
	  <xsl:choose>
		<xsl:when test="count($pids) &gt; 1">
		<xsl:for-each select="$pids">
		  <th>
		    <xsl:value-of select="." /><br/>
		    <xsl:value-of select="$ass/label" /><br/>
		    <xsl:value-of select="$ass/date" />
		  </th>
		</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
		  <th>
		    <xsl:value-of select="$bids[$index]" /><br/>
		    <xsl:value-of select="$ass/label" /><br/>
		    <xsl:value-of select="$ass/date" />
		  </th>
		</xsl:otherwise>
	  </xsl:choose>
  </xsl:if>

  
	<xsl:call-template name="headercell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="ass" select="$ass"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



</xsl:stylesheet>
