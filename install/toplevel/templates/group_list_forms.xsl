<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  xmlns:set="http://exslt.org/sets">

<xsl:output method="html" />

<xsl:template match="text()" />

<xsl:template match="students">
  <xsl:variable name="fids" select="set:distinct(group/student/registrationgroup/value)" />

  <xsl:call-template name="forms">
	<xsl:with-param name="index" select="count($fids)"/>
	<xsl:with-param name="fids" select="$fids"/>
  </xsl:call-template>

</xsl:template>


<xsl:template name="forms">
  <xsl:param name="index"></xsl:param>
  <xsl:param name="fids"></xsl:param>

  <xsl:if test="$index &gt; 0">

	  <xsl:call-template name="form">
		<xsl:with-param name="grids" select="set:distinct(group[student/registrationgroup/value=$fids[$index]]/name/value)">
		</xsl:with-param>
		<xsl:with-param name="fid" select="$fids[$index]"/>
	  </xsl:call-template>

	  <xsl:if test="$index!=1">
		<div class="spacer"><xsl:text>&#160;</xsl:text></div>
		<hr />
		<div class="spacer"><xsl:text>&#160;</xsl:text></div>
	  </xsl:if>


    <xsl:call-template name="forms">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="fids" select="$fids"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>




<xsl:template name="form">
  <xsl:param name="grids"></xsl:param>
  <xsl:param name="fid"></xsl:param>

  <div id="logo">
	<img src="../images/schoollogo.png" width="130px"/>
  </div>

  <div class="right">
	<table>
	  <tr>
		<th>
		  <label>
			Form
		  </label>
		  <xsl:value-of select="$fid" />&#160;
		</th>
	  </tr>
	  <tr>
		<td style="border:0 none;">
		  <xsl:value-of select="group/date/value" />&#160;
		</td>
	  </tr>
	</table>
  </div>

  <div class="spacer">
	<xsl:text>&#160;</xsl:text>
  </div>

	<xsl:call-template name="groups">
	  <xsl:with-param name="index" select="count($grids)"/>
	  <xsl:with-param name="grids" select="$grids"/>
	  <xsl:with-param name="fid" select="$fid"/>
	</xsl:call-template>

</xsl:template>



<xsl:template name="groups">
  <xsl:param name="index"></xsl:param>
  <xsl:param name="grids"></xsl:param>
  <xsl:param name="fid"></xsl:param>

  <xsl:if test="$index &gt; 0">

	<table class="fullwidth grades">
	  <tr>
		<th colspan="5" style="text-align:center;">
<!--
		  <xsl:value-of select="group[name/value=$grids[$index]]/day/value" />
		  <xsl:text>&#160;</xsl:text>
-->
		  <xsl:value-of select="$grids[$index]" />
		</th>
	  </tr>


	  <xsl:for-each select="set:distinct(group[name/value=$grids[$index]]/student[registrationgroup/value=$fid])">
		<tr>
		  <th>
			<xsl:value-of select="position()" />
		  </th>
		  <td>
			<xsl:value-of select="enrolnumber/value/text()" />
		  </td>
		  <td>
			<xsl:value-of select="displayfullsurname/value/text()" />&#160;
		  </td>
		  <td>
			<xsl:text>&#160;</xsl:text>
		  </td>
		  <td>
			<xsl:text>&#160;</xsl:text>
		  </td>
		</tr>
	  </xsl:for-each>

	  <tr>
		<th colspan="8" class="spacer">
		</th>
	  </tr>

	</table>

    <xsl:call-template name="groups">
	  <xsl:with-param name="index" select="$index - 1"/>
	  <xsl:with-param name="grids" select="$grids"/>
	  <xsl:with-param name="fid" select="$fid"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



</xsl:stylesheet>
