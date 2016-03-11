<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="students">
  <xsl:apply-templates select="transport"/>
</xsl:template>

<xsl:template match="transport">
  <xsl:variable name="busname">
	<xsl:value-of select="name/value/text()" />
  </xsl:variable>
  <xsl:variable name="listtype">
	<xsl:value-of select="type/value/text()" />
  </xsl:variable>

  <div id='logo'>
	<img src="../images/schoollogo.png" width="130px"/>
  </div>

  <div class="right">
	<table>
	  <tr>
		<th>
		  <label>
			Transport
		  </label>
		  <xsl:value-of select="$busname" />&#160;
		</th>
	  </tr>
	</table>
  </div>

  <div class="spacer">
	<xsl:text>&#160;</xsl:text>
  </div>

  <xsl:apply-templates select="journey[direction='I']">
	<xsl:with-param name="busname" select="$busname"/>
  </xsl:apply-templates>

  <div class="spacer"><xsl:text>&#160;</xsl:text></div>
  <div class="spacer"><xsl:text>&#160;</xsl:text></div>

  <xsl:apply-templates select="journey[direction='O']">
	<xsl:with-param name="busname" select="$busname"/>
  </xsl:apply-templates>


  <xsl:if test="position()!=last()">
	<div class="spacer"><xsl:text>&#160;</xsl:text></div>
	<hr />
	<div class="spacer"><xsl:text>&#160;</xsl:text></div>
  </xsl:if>


</xsl:template>




<xsl:template match="journey">
  <xsl:param name="busname"></xsl:param>


  <table class="fullwidth grades">
	<tr>
	  <th colspan="2">
		<xsl:value-of select="$busname" />&#160;
		<xsl:choose>
		  <xsl:when test="direction='I'">
			<xsl:text>AM</xsl:text>
		  </xsl:when>
		  <xsl:when test="direction='O'">
			<xsl:text>PM</xsl:text>
		  </xsl:when>
		</xsl:choose>
	  </th>
	  <th>Departure: &#160;
		<xsl:value-of select="bus/time" />&#160;
	  </th>
	</tr>
	<tr>
	  <th colspan="2" style="width:75%;">
		<xsl:text>Stop</xsl:text>
	  </th>
	  <th>
		<xsl:text>Time</xsl:text>
	  </th>
	</tr>

	<xsl:apply-templates select="stops/stop">
	  <xsl:sort select="stops/stop/sequence/text()" data-type="number" order="ascending"/>
	</xsl:apply-templates>

  </table>

</xsl:template>




<xsl:template match="stop">
  <tr>
	<th>
	  <xsl:value-of select="sequence/text()" />&#160;
	</th>
	<td>
	  <xsl:value-of select="name/text()" />&#160;
	</td>
	<td>
	  <xsl:value-of select="departuretime/text()" />&#160;
	</td>
  </tr>
</xsl:template>


</xsl:stylesheet>
