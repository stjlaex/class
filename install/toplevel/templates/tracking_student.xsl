<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

<xsl:output method="html" />

<xsl:template match="students">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
  <xsl:variable name="crids" select="set:distinct(assessments/assessment/course/value)" />


  <xsl:call-template name="coursetable">
	<xsl:with-param name="index" select="1"/>
	<xsl:with-param name="crids" select="$crids"/>
  </xsl:call-template>
			
  <div class="spacer"></div>
</xsl:template>


<xsl:template name="coursetable">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="crids"></xsl:param>

  <xsl:variable name="maxindex" select="count($crids)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<xsl:variable name="years" select="set:distinct(assessments/assessment[course/value=string($crids[$index])]/year/value)" />

	<xsl:choose>
	  <xsl:when test="$crids[$index]='%'">
		<!--	  <xsl:text>General</xsl:text> -->
	  </xsl:when>
	  <xsl:otherwise>

		<xsl:call-template name="yeartable">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="crid" select="$crids[$index]"/>
		  <xsl:with-param name="years" select="$years"/>
		</xsl:call-template>

	  </xsl:otherwise>
	</xsl:choose>


    <xsl:call-template name="coursetable">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="crids" select="$crids"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>






<xsl:template name="yeartable">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="crid"></xsl:param>
  <xsl:param name="years"></xsl:param>

  <xsl:variable name="maxindex" select="count($years)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<xsl:variable name="bids" select="set:distinct(assessments/assessment[year/value=string($years[$index])][course/value=string($crid)]/subject/value)" />
	<xsl:variable name="eids" select="set:distinct(assessments/assessment[year/value=string($years[$index])][course/value=string($crid)][element/value!='HP' and element/value!='DT1' and element/value!='NC2' and element/value!='ISA' and element/value!='KS2']/id_db)" />


	<div class="head">
	  <label><xsl:value-of select="displayfullsurname/value/text()" /></label>
	  <label><xsl:value-of select="registrationgroup/value/text()" /></label>
	</div>
	<div>
	  <div class="head">
		<label>
		  <xsl:value-of select="$crid"/><xsl:text>&#160; </xsl:text>
		  <xsl:value-of select="$years[$index]"/>
		</label>
	  </div>
	  <table>
		<tr>
		  <th>
		  </th>
		  <xsl:call-template name="assheader">
			<xsl:with-param name="index" select="1"/>
			<xsl:with-param name="eids" select="$eids"/>
		  </xsl:call-template>
		</tr>

		<xsl:call-template name="subjectrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="crid" select="$crid"/>
		  <xsl:with-param name="bids" select="$bids"/>
		  <xsl:with-param name="eids" select="$eids"/>
		</xsl:call-template>

	  </table>
	</div>

	<div class="spacer"></div>
	<hr />
	<div class="spacer"></div>

    <xsl:call-template name="yeartable">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="crid" select="$crid"/>
	  <xsl:with-param name="years" select="$years"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>







<xsl:template name="assheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="eids"></xsl:param>

  <xsl:variable name="maxindex" select="count($eids)" />

  <xsl:if test="$index &lt; $maxindex+1">
	  <th>
	  <label>
		<xsl:value-of select="../asstable/ass[id_db=string($eids[$index])]/name" /><br />
		<xsl:value-of select="../asstable/ass[id_db=string($eids[$index])]/element" /><br />
		<xsl:value-of select="../asstable/ass[id_db=string($eids[$index])]/date" />
	  </label>
	  </th>
    <xsl:call-template name="assheader">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="eids" select="$eids"/>
	</xsl:call-template>
  </xsl:if>

</xsl:template>




<xsl:template name="subjectrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="crid"></xsl:param>
  <xsl:param name="bids"></xsl:param>
  <xsl:param name="eids"></xsl:param>

  <xsl:variable name="maxindex" select="count($bids)" />

  <xsl:if test="$index &lt; $maxindex+1">


	<xsl:variable name="subjectstrands" select="set:distinct(assessments/assessment[course/value=string($crid)][subject/value=string($bids[$index])]/component)" />

	<xsl:call-template name="strandrow">
	  <xsl:with-param name="index" select="1"/>
	  <xsl:with-param name="crid" select="$crid"/>
	  <xsl:with-param name="bid" select="$bids[$index]"/>
	  <xsl:with-param name="subjectstrands" select="$subjectstrands"/>
	  <xsl:with-param name="eids" select="$eids"/>
	</xsl:call-template>

    <xsl:call-template name="subjectrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="crid" select="$crid"/>
	  <xsl:with-param name="bids" select="$bids"/>
	  <xsl:with-param name="eids" select="$eids"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>




<xsl:template name="strandrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="crid"></xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="subjectstrands"></xsl:param>
  <xsl:param name="eids"></xsl:param>

  <xsl:variable name="maxindex" select="count($subjectstrands)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<tr>
	  <th>
	  <label>
		<xsl:value-of select="$bid"/>

		<xsl:value-of select="$subjectstrands[$index]/value"/>
	  </label>
	  </th>
	  <xsl:call-template name="asscell">
		<xsl:with-param name="index" select="1"/>
		<xsl:with-param name="crid" select="$crid"/>
		<xsl:with-param name="bid" select="$bid"/>
		<xsl:with-param name="strandid" select="$subjectstrands[$index]/id"/>
		<xsl:with-param name="eids" select="$eids"/>
	  </xsl:call-template>
	</tr>


	<xsl:call-template name="strandrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="crid" select="$crid"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="subjectstrands" select="$subjectstrands"/>
	  <xsl:with-param name="eids" select="$eids"/>
	</xsl:call-template>
  
  </xsl:if>

</xsl:template>





<xsl:template name="asscell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="crid"></xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="strandid"></xsl:param>
  <xsl:param name="eids"></xsl:param>

  <xsl:variable name="maxindex" select="count($eids)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<td>
	  <xsl:for-each select="assessments/assessment[course/value=string($crid)][component/id=string($strandid)][id_db=string($eids[$index])][subject/value=string($bid)]">
		<xsl:value-of select="result/value" />
		<xsl:text>&#160; </xsl:text>	
	  </xsl:for-each>
	</td>


    <xsl:call-template name="asscell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="crid" select="$crid"/>
	  <xsl:with-param name="bid" select="$bid"/>
	  <xsl:with-param name="strandid" select="$strandid"/>
	  <xsl:with-param name="eids" select="$eids"/>
	</xsl:call-template>

  </xsl:if>


</xsl:template>





</xsl:stylesheet>
