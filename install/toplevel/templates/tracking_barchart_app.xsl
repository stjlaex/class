<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  
				xmlns="http://www.w3.org/1999/xhtml" xmlns:set="http://exslt.org/sets">
<!-- -->


<xsl:output method="html" />



<xsl:template match="students">

  <div class="head">
	<div class="left">
	  <h2>APP Progress by Term</h2>
	</div>
	<div>
	</div>
	<div>
	</div>
  </div>

  <div class="spacer"></div>

  <div id="chart" class="center"></div>

  <div class='spacer'></div>

  <xsl:apply-templates select="student">
	<xsl:with-param name="cols" select="set:distinct(student/assessments/assessment/point)"/>
  </xsl:apply-templates>

  <hr />

</xsl:template>



<xsl:template match="student">
  <xsl:param name="cols"></xsl:param>

  <div class="hidden">
	<xsl:element name="table">
	  <xsl:attribute name="id"><xsl:value-of select="displayfullname/value" /></xsl:attribute>
	  <tr>
		<td class="name"><xsl:value-of select="displayfullname/value" /></td>
	  </tr>
	  <tr>
		<th><label></label></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <tr>
		<th class="xlabel">
<!--
		  <xsl:value-of select="name" />
-->
		</th>	
		<xsl:call-template name="cell">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>


	  <tr>
		<th><label>TOTAL</label></th>
		<xsl:call-template name="totalrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>
	</xsl:element>
  </div>

</xsl:template>





<xsl:template name="cell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>

  <xsl:variable name="maxindex" select="count($cols)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<xsl:element name="td">
	  <xsl:attribute name="class">value</xsl:attribute>
	  <xsl:value-of select="sum(assessments/assessment[point=string($cols[$index])]/value)" />
	</xsl:element>

    <xsl:call-template name="cell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>
  </xsl:if>
</xsl:template>





<xsl:template name="headerrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>

  <xsl:variable name="maxindex" select="count($cols)" />

  <xsl:if test="$index &lt; $maxindex+1">

	<th>
	  <xsl:value-of select="$cols[$index]" />
<!--
	  <xsl:if test="tables/table/cols/col[value=$cols[$index]]/date">
		<br /><label><xsl:value-of select="tables/table/cols/col[value=$cols[$index]]/date" /></label>
	  </xsl:if>
-->
	</th>
  
	<xsl:call-template name="headerrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>






<xsl:template name="totalrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"></xsl:param>

  <xsl:variable name="maxindex" select="count($cols)" />

  <xsl:if test="$index &lt; $maxindex+1">
	<xsl:variable name="code"><xsl:value-of select="$cols[$index]" />:</xsl:variable>

	<xsl:element name="td">
	  <xsl:attribute name="class">total</xsl:attribute>
	  <xsl:value-of select="round(sum(../student/assessments/assessment[point=string($cols[$index])]/value) div count(../student[assessments/assessment/point=string($cols[$index])]))" />
	</xsl:element>

	<xsl:call-template name="totalrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>

</xsl:stylesheet>
