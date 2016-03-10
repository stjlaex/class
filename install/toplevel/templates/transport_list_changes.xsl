<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

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
	  <th style="background-color:#999;color:#fff;text-align:center;" colspan="5">
		<xsl:text>ON &#160;</xsl:text>
		<xsl:value-of select="day/value/text()" />
		<xsl:text>&#160; afternoon bus today only</xsl:text>
	  </th>
	</tr>
	<tr>
	  <th colspan="3" style="width:40%;">
		<xsl:text>&#160;</xsl:text>
	  </th>
	  <th>
		<xsl:text>1st Contact</xsl:text>
	  </th>
	  <th>
		<xsl:text>2nd Contact</xsl:text>
	  </th>
	</tr>
	<tr>
	  <th colspan="5"  class="label">
		<xsl:text>NOT REGULAR BUS</xsl:text>
	  </th>
	</tr>
	<xsl:apply-templates select="student[otherjourney[bus/value!=$busname]/direction='O' and journey[bus/value=$busname]/direction='O']">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">stop</xsl:with-param>
	</xsl:apply-templates>

	<xsl:apply-templates select="student[not(otherjourney) and journey[bus/value=$busname and day!='%']/direction='O']">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">stop</xsl:with-param>
	</xsl:apply-templates>
	<tr>
	  <th colspan="5"  class="label">
		<xsl:text>DIFFERENT STOP</xsl:text>
	  </th>
	</tr>
	<xsl:apply-templates select="student[otherjourney[bus/value=$busname][stop/value]/direction='O' and journey[bus/value=$busname]/direction='O']">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">stop</xsl:with-param>
	</xsl:apply-templates>




	<tr>
	  <th colspan="5" class="spacer">
	  </th>
	</tr>
	<tr>
	  <th style="background-color:#999;color:#fff;text-align:center;" colspan="5">
		<xsl:text>NOT &#160;</xsl:text>
		<xsl:value-of select="day/value/text()" />
		<xsl:text>&#160; afternoon bus today only</xsl:text>
	  </th>
	</tr>
	<tr>
	  <th colspan="3" style="width:40%;">
		<xsl:text>&#160;</xsl:text>
	  </th>
	  <th>
		<xsl:text>1st Contact</xsl:text>
	  </th>
	  <th>
		<xsl:text>2nd Contact</xsl:text>
	  </th>
	</tr>
	<tr>
	  <th colspan="5"  class="label">
		<xsl:text>DOING CLUBS</xsl:text>
	  </th>
	</tr>
	<xsl:apply-templates select="student[club/value and (journey[bus/value=$busname]/direction='O' or otherjourney[bus/value=$busname]/direction='O')]">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">club</xsl:with-param>
	</xsl:apply-templates>	
	<tr>
	  <th colspan="5" class="spacer">
	  </th>
	</tr>
	<tr>
	  <th colspan="5" class="label">
		<xsl:text>ABSENT</xsl:text>
	  </th>
	</tr>
	<xsl:apply-templates select="student[attendances/attendance[session/value='PM'][code/value!='U' and code/value!='UB' and code/value!='UA']/status/value='a']">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	</xsl:apply-templates>
	<xsl:apply-templates select="student[attendances/attendance[session/value='AM'][code/value!='U' and code/value!='UB' and code/value!='UA']/status/value='a' and not(attendances/attendance/session/value='PM')]">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">att</xsl:with-param>
	</xsl:apply-templates>
	<tr>
	  <th colspan="5" class="spacer">
	  </th>
	</tr>
	<tr>
	  <th colspan="5" class="label">
		<xsl:text>OTHER BUS OR BEING COLLECTED</xsl:text>
	  </th>
	</tr>
<!--
	<xsl:apply-templates select="student[not(journey/direction='O')]">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">bus</xsl:with-param>
	</xsl:apply-templates>
-->
	<xsl:apply-templates select="student[not(journey[bus/value=$busname]/direction='O') and journey/direction='O' and otherjourney[bus/value=$busname]/direction='O']">
	  <xsl:sort select="journey[direction='O']/stop/sequence/text()" data-type="number" order="ascending"/>
	  <xsl:with-param name="disfield">bus</xsl:with-param>
	</xsl:apply-templates>



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
  <xsl:param name="disfield"> </xsl:param>
  <xsl:variable name="busname">
	<xsl:value-of select="../name/value" />
  </xsl:variable>
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
		<xsl:choose>
		  <xsl:when test="$disfield='club'">
			<xsl:text>&#160;</xsl:text>
			<xsl:value-of select="club/value" />
		  </xsl:when>
		  <xsl:when test="$disfield='bus'">
			<xsl:text>&#160;</xsl:text>
			<xsl:value-of select="journey[direction='O']/bus/value" />
		  </xsl:when>
		  <xsl:when test="$disfield='stop'">
			<xsl:text>&#160;</xsl:text>
			<xsl:value-of select="journey[direction='O']/stop/value" />
		  </xsl:when>
		  <xsl:otherwise>
			<text>&#160;</text>
			<xsl:text>&#160;</xsl:text>
		  </xsl:otherwise>
		</xsl:choose>

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


</xsl:stylesheet>
