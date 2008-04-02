<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->


<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
  <xsl:apply-templates select="student" />
</xsl:template>

<xsl:template match="student">
  <div class="studenthead">
	<div style="background-color:#fff;">
	  <label>&#160;</label>
	  Registration Report
	</div>
	<div>
	  <label>Student</label>
	  <xsl:value-of select="displayfullname/value/text()" />&#160;
	</div>
	<div>
	  <label>Form</label>
	  <xsl:value-of select="registrationgroup/value/text()" />
	</div>
  </div>

  <div class="right">
	<table>
	  <tr>
		<th>
		  Summary&#160;
		  <label>
			<xsl:value-of select="attendance/summary/startdate/value/text()" />&#160;
			<xsl:value-of select="attendance/summary/enddate/value/text()" />&#160;
		  </label>
		</th>
		<th>
		  Sessions
		</th>
		<th>
		  Percent
		</th>
	  </tr>
	  <tr>
		<td>Attended</td>
		<td>
		<div><xsl:value-of select="attendance/summary/attended/value/text()" />&#160;</div>
		</td>
		<td>
		<div><xsl:value-of select="round(attendance/summary/attended/value/text() div (attendance/summary/attended/value/text() + attendance/summary/absentauthorised/value/text() + attendance/summary/absentunauthorised/value/text()) *100)" />&#160;%</div>
		</td>
	  </tr>
	  <tr>
		<td>Authorised absences</td>
		<td>
		<div><xsl:value-of select="attendance/summary/absentauthorised/value/text()" />&#160;</div>
		</td>
	  </tr>
	  <tr>
		<td>Unauthorised absences</td>
		<td>
		<div><xsl:value-of select="attendance/summary/absentunauthorised/value/text()" />&#160;</div>
		</td>
	  </tr>
	  <tr>
		<td>Possible total</td>
		<td>
		<div><xsl:value-of select="attendance/summary/attended/value/text() + attendance/summary/absentauthorised/value/text() + attendance/summary/absentunauthorised/value/text()" /></div>
		</td>
	  </tr>
	  <tr>
		<th>
		  Including&#160;
		</th>
		<th>
		</th>
		<th>
		</th>
	  </tr>
	  <tr>
		<td>Late after registration (Unauthorised)</td>
		<td>
		<div><xsl:value-of select="attendance/summary/lateunauthorised/value/text()" />&#160;</div>
		</td>
	  </tr>
	  <tr>
		<td>Late after registration (Authorised)</td>
		<td>
		<div><xsl:value-of select="attendance/summary/lateauthorised/value/text()" />&#160;</div>
		</td>
	  </tr>
	  <tr>
		<td>Unexplained absences</td>
		<td>
		<div><xsl:value-of select="attendance/summary/notexplained/value/text()" />&#160;</div>
		</td>
	  </tr>
	</table>
  </div>


  <div class="left" style="height:800px;">
	<table>
	  <tr>
		<th><label>Week beginning</label></th>
		<th>&#160;</th>
	  </tr>
  <xsl:variable name="eventid">
	<xsl:value-of select="../../attendanceevent/id_db" />
  </xsl:variable>
  <tr>
	<td>
	  <xsl:variable name="status">
		<xsl:value-of select="attendances/attendance/status/value" />
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
</table>
</div>

  <div class="right">
	<table class="small">
	  <tr>
		<th>Code</th>
		<th></th>
	  </tr>
	  <tr>
		<td>#</td>
		<td><label>School closed to pupils</label></td>
	  </tr>
	  <tr>
		<td>/</td>
		<td><label>Present (AM)</label></td>
	  </tr>
	  <tr>
		<td>\</td>
		<td><label>Present (PM)</label></td>
	  </tr>
	  <tr>
		<td>L</td>
		<td><label>Late (after register closed - Authorised)</label></td>
	  </tr>
	  <tr>
		<td>U</td>
		<td><label>Late (after register closed - Unauthorised)</label></td>
	  </tr>
	  <tr>
		<td>B</td>
		<td><label>Educated off site</label></td>
	  </tr>
	  <tr>
		<td>C</td>
		<td><label>Other authorised circumstances</label></td>
	  </tr>
	  <tr>
		<td>E</td>
		<td><label>Excluded</label></td>
	  </tr>
	  <tr>
		<td>F</td>
		<td><label>Extended family holiday (agreed)</label></td>
	  </tr>
	  <tr>
		<td>G</td>
		<td><label>Family holiday (not agreed)</label></td>
	  </tr>
	  <tr>
		<td>H</td>
		<td><label>Family holiday (agreed)</label></td>
	  </tr>
	  <tr>
		<td>I</td>
		<td><label>Illness (not medical or dental appointments)</label></td>
	  </tr>
	  <tr>
		<td>J</td>
		<td><label>Interview</label></td>
	  </tr>
	  <tr>
		<td>M</td>
		<td><label>Medical / Dental appointments</label></td>
	  </tr>
	  <tr>
		<td>N</td>
		<td><label>No reason provided for absence</label></td>
	  </tr>
	  <tr>
		<td>O</td>
		<td><label>Unauthorised absence (not covered by any other code)</label></td>
	  </tr>
	  <tr>
		<td>P</td>
		<td><label>Approved sporting activity</label></td>
	  </tr>
	  <tr>
		<td>R</td>
		<td><label>Religious observance</label></td>
	  </tr>
	  <tr>
		<td>S</td>
		<td><label>Study leave</label></td>
	  </tr>
	  <tr>
		<td>T</td>
		<td><label>Traveller absence</label></td>
	  </tr>
	  <tr>
		<td>V</td>
		<td><label>Educational visit or trip</label></td>
	  </tr>
	  <tr>
		<td>W</td>
		<td><label>Work experience</label></td>
	  </tr>
	  <tr>
		<td>X</td>
		<td><label>Non-compulsory school age</label></td>
	  </tr>
	  <tr>
		<td>Y</td>
		<td><label>Enforced school closure</label></td>
	  </tr>
	  <tr>
		<td>Z</td>
		<td><label>Pupil not yet on roll</label></td>
	  </tr>
	</table>
  </div>

<div class='spacer'></div>
<hr />
</xsl:template>


</xsl:stylesheet>
