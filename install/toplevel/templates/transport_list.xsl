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

  <div id='logo'>
	<img src="../images/schoollogo.png" height="70px"/>
  </div>

  <div class="right">
	<table>
	  <tr>
		<th>

		  <xsl:choose>
			<xsl:when test="type/value='f'">
			  <label>
				Form
			  </label>
			</xsl:when>
			<xsl:otherwise>
			  <label>
				Transport
			  </label>			  
			</xsl:otherwise>
		  </xsl:choose>

		  <xsl:value-of select="name/value/text()" />&#160;


		</th>
	  </tr>
	  <tr>	
		<th>
		  <label>
			Session
		  </label>
		  <xsl:value-of select="day/value/text()" />&#160;
		  <xsl:value-of select="date/value/text()" />&#160;
		</th>
	  </tr>
	</table>
  </div>

  <div class="spacer">
	<xsl:text>&#160;</xsl:text>
  </div>

  <table class="fullwidth grades">
	<tr>
	  <th colspan="2" style="width:25%;">
		<xsl:text></xsl:text>
	  </th>
	  <th>
		<xsl:text>Stop AM</xsl:text>
	  </th>
	  <th>
		<xsl:text>Stop PM</xsl:text>
	  </th>
	</tr>


	<xsl:choose>
	  <xsl:when test="type/value='f'">
		<xsl:apply-templates select="student">
		  <xsl:sort select="journey[direction='I']/bus/id_db/text()" data-type="number" order="ascending"/>
		  <xsl:sort select="surname/value/text" order="ascending"/>
		</xsl:apply-templates>
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:apply-templates select="student">
		  <xsl:sort select="journey[direction='I']/stop/sequence/text()" data-type="number" order="ascending"/>
		  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
		</xsl:apply-templates>
	  </xsl:otherwise>
	</xsl:choose>


	<tr>
	  <th colspan="2" style="width:25%;">
	  </th>
	  <th>
		<xsl:text>Total Students: &#160;</xsl:text>		
		<xsl:value-of select="count(student/journey[direction='I'])" />
	  </th>
	  <th>
		<xsl:text>Total Students: &#160;</xsl:text>		
		<xsl:value-of select="count(student/journey[direction='O'])" />
	  </th>
	</tr>
  </table>




  <xsl:if test="position()!=last()">
	<div class="spacer">
	  <xsl:text>&#160;</xsl:text>
	</div>
	<hr />
	<div class="spacer">
	  <xsl:text>&#160;</xsl:text>
	</div>
  </xsl:if>


</xsl:template>

<xsl:template match="student">
  <xsl:variable name="listtype">
	<xsl:value-of select="../type/value" />
  </xsl:variable>

  <tr>
	<th>
	  <xsl:value-of select="displayfullname/value/text()" />&#160;
	</th>
	<th>
	  <xsl:value-of select="registrationgroup/value/text()" />
	</th>
	<td>
		  <xsl:if test="journey[direction='I']">

			<xsl:if test="$listtype='f'">
			  <span style="font-weight:900;font-size:10pt;">
				<xsl:value-of select="journey[direction='I']/bus/value/text()" />
			  </span><br />
			</xsl:if>

			<xsl:text>&#160;(</xsl:text>
			<xsl:value-of select="journey[direction='I']/stop/sequence/text()" />
			<xsl:text>)&#160;</xsl:text>
			<xsl:value-of select="journey[direction='I']/stop/value/text()" />
			<div style="float:right;"><xsl:value-of select="journey[direction='I']/stop/departuretime" /></div>
		  </xsl:if>
	</td>
	<td>
		  <xsl:if test="journey[direction='O']">
			<xsl:if test="$listtype='f'">
			  <span style="font-weight:900;font-size:10pt;">
				<xsl:value-of select="journey[direction='O']/bus/value/text()" />
			  </span><br />
			</xsl:if>
			<xsl:text>&#160;(</xsl:text>
			<xsl:value-of select="journey[direction='O']/stop/sequence" />
			<xsl:text>)&#160;</xsl:text>
			<xsl:value-of select="journey[direction='O']/stop/value/text()" />
			<div style="float:right;"><xsl:value-of select="journey[direction='O']/stop/departuretime" /></div>
		  </xsl:if>
	</td>
  </tr>
</xsl:template>


</xsl:stylesheet>
