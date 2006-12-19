<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->


<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="community"/>
</xsl:template>

<xsl:template match="community">

  <div id='logo'>
	<img src="../images/schoollogo.png" width="130px"/>
  </div>

  <div class="studenthead">
	<table id="studentdata">
	  <tr>
		<td>
		  <label>
			Registration
		  </label>
		</td>
	  </tr>
	  <tr>	
		<td>
		  <label>
			Session:
		  </label>
		  <xsl:value-of select="../attendanceevent/date/value/text()" />&#160;
		  <xsl:value-of select="../attendanceevent/period/value/text()" />&#160;
		</td>
	  </tr>
	</table>
  </div>

  <div class='spacer'></div>

  <table class="grades" id="fullwidth">
	<tr>
	  <th colspan="2">
		<xsl:text>Student</xsl:text>
	  </th>
	  <th>
		<xsl:text>Attendance</xsl:text>
	  </th>
	</tr>
	<xsl:apply-templates select="student" />
  </table>

  <div class='spacer'></div>

  <hr />
</xsl:template>

<xsl:template match="student">
  <xsl:variable name="eventid">
	<xsl:value-of select="../../attendanceevent/id_db" />
  </xsl:variable>
  <tr>
	<th>
	  <xsl:value-of select="displayfullname/value/text()" />&#160;
	</th>
	<th>
	  <xsl:value-of select="registrationgroup/value/text()" />
	</th>
	<td>
	  <xsl:variable name="status">
		<xsl:value-of select="attendances/attendnace/status/value" />
	  </xsl:variable>
	  <xsl:choose>
		<xsl:when test="$status='p'">
		  <img src="../class/images/forstroke.png" />
		</xsl:when>
		<xsl:when test="$status='a'">
		  <img src="../class/images/ostroke.png" />
		</xsl:when>
		<xsl:otherwise>
		  <xsl:text>&#160;</xsl:text>
		</xsl:otherwise>
	  </xsl:choose>
	</td>
  </tr>
</xsl:template>


</xsl:stylesheet>
