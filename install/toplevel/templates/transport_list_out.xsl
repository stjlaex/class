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
	  <tr>	
		<th>
		  <label>
			PM Session
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
	  <th colspan="2">
		<xsl:text>Attendance</xsl:text>
	  </th>
	  <th>
		<xsl:text>Stop PM</xsl:text>
	  </th>
	  <th>
		<xsl:text>Stop AM</xsl:text>
	  </th>
	  <th>
		<xsl:text>1st Contact</xsl:text>
	  </th>
	  <th>
		<xsl:text>2nd Contact</xsl:text>
	  </th>
	</tr>

	<xsl:choose>
	  <xsl:when test="contains(name/value,'Late')">
		<xsl:apply-templates select="student">
		  <xsl:sort select="journey[bus/value=$busname][direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
		</xsl:apply-templates>
	  </xsl:when>
	  <xsl:when test="$listtype='f'">
		<xsl:apply-templates select="student[not(club/value) and journey/direction='O']">
		  <xsl:sort select="journey[direction='O']/bus/value" order="ascending"/>
		  <xsl:sort select="surname/value" order="ascending"/>
		</xsl:apply-templates>
	  </xsl:when>
	  <xsl:otherwise>
		<xsl:apply-templates select="student[not(club/value) and journey[bus/value=$busname]/direction='O']">
		  <xsl:sort select="journey[bus/value=$busname][direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
		</xsl:apply-templates>
	  </xsl:otherwise>
	</xsl:choose>

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
  <xsl:variable name="busname">
	<xsl:value-of select="../name/value" />
  </xsl:variable>
  <xsl:variable name="listtype">
	<xsl:value-of select="../type/value/text()" />
  </xsl:variable>
  <tr>
	<th>
	  <xsl:value-of select="displayfullname/value/text()" />&#160;
	</th>
	<th>
	  <xsl:value-of select="registrationgroup/value/text()" />
	</th>
	<td class="box">
	  <xsl:variable name="amstatus">
		<xsl:value-of select="attendances/attendance[session/value='AM']/status/value" />
	  </xsl:variable>
	  <xsl:variable name="amcode">
		<xsl:value-of select="attendances/attendance[session/value='AM']/code/value" />
	  </xsl:variable>
	  <xsl:choose>
		<xsl:when test="$amstatus='p'">
		  <xsl:text>/</xsl:text>
		</xsl:when>
		<xsl:when test="$amstatus='a' and $amcode='O'">
		  <xsl:text>O</xsl:text>
		</xsl:when>
		<xsl:when test="$amstatus='a'">
		  <xsl:value-of select="$amcode" />
		</xsl:when>
		<xsl:otherwise>
		  <xsl:text>&#160;</xsl:text>
		</xsl:otherwise>
	  </xsl:choose>

	</td>
	<td class="box">
	  <xsl:variable name="pmstatus">
		<xsl:value-of select="attendances/attendance[session/value='PM']/status/value" />
	  </xsl:variable>
	  <xsl:variable name="pmcode">
		<xsl:value-of select="attendances/attendance[session/value='PM']/code/value" />
	  </xsl:variable>
	  <xsl:choose>
		<xsl:when test="$pmstatus='p'">
		  <xsl:text>\</xsl:text>
		</xsl:when>
		<xsl:when test="$pmstatus='a' and $pmcode='O'">
		  <xsl:text>O</xsl:text>
		</xsl:when>
		<xsl:when test="$pmstatus='a'">
		  <xsl:value-of select="$pmcode" />
		</xsl:when>
		<xsl:otherwise>
		  <xsl:text>&#160;</xsl:text>
		</xsl:otherwise>
	  </xsl:choose>
	</td>
	<td>
		  <xsl:if test="$listtype='f' and journey[direction='O']">
			<xsl:text>&#160;(</xsl:text>
			<xsl:value-of select="journey[direction='O']/bus/value/text()" />
			<xsl:text>&#160;)</xsl:text>
		  </xsl:if>
		  <xsl:if test="journey[bus/value=$busname][direction='O']">
			<xsl:text>&#160;(</xsl:text>
			<xsl:value-of select="journey[bus/value=$busname][direction='O']/stop/sequence" />
			<xsl:text>)&#160;</xsl:text>
			<xsl:value-of select="journey[bus/value=$busname][direction='O']/stop/value/text()" />
			<xsl:apply-templates select="journey[bus/value=$busname][direction='O']/comment"/>
		  </xsl:if>
	</td>
	<td>
		  <xsl:if test="journey[bus/value=$busname][direction='I']">
			<xsl:text>&#160;(</xsl:text>
			<xsl:value-of select="journey[bus/value=$busname][direction='I']/stop/sequence/text()" />
			<xsl:text>)&#160;</xsl:text>
			<xsl:value-of select="journey[bus/value=$busname][direction='I']/stop/value/text()" />
			<xsl:apply-templates select="journey[bus/value=$busname][direction='I']/comment"/>
		  </xsl:if>
		  <xsl:if test="journey[bus/value!=$busname][direction='I']">
			<xsl:text>&#160;(</xsl:text>
			<xsl:value-of select="journey[bus/value!=$busname][direction='I']/bus/value/text()" />
			<xsl:text>)&#160;</xsl:text>
		  </xsl:if>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	  <xsl:if test="$listtype!='f'">
		<xsl:value-of select="firstcontactphone/value/text()" />
	  </xsl:if>
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	  <xsl:if test="$listtype!='f'">
		<xsl:value-of select="secondcontactphone/value/text()" />
	  </xsl:if>
	</td>
  </tr>
</xsl:template>

<xsl:template match="comment">
  <br />
  <h2><xsl:value-of select="value/text()" /></h2>
</xsl:template>

</xsl:stylesheet>
