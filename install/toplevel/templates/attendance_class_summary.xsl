<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>


<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="students">
  <xsl:apply-templates select="student"/>
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

	  (<xsl:value-of select="registrationgroup/value/text()" />)
	</div>
	<div>
	  <label>Subject Class</label>
	  <xsl:value-of select="../class/text()" />
	</div>
  </div>

  <div class="right">
	<table>
	  <tr>
		<th>
		  <label>
			Summary
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
		<td>Late to registration</td>
		<td>
		<div><xsl:value-of select="attendance/summary/latetoregister/value/text()" />&#160;</div>
		</td>
	  </tr>
	  <tr>
		<td>Late after registration <br />(unauthorised)</td>
		<td>
		<div><xsl:value-of select="attendance/summary/lateunauthorised/value/text()" />&#160;</div>
		</td>
	  </tr>
	  <tr>
		<td>Late after registration <br />(authorised)</td>
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


  <div class="left small allsmall" style="height:800px;">
<!--
	<table>
	  <tr>
		<th><label>Week beginning</label></th>
		<th colspan="7">&#160;</th>
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
-->
	<xsl:copy-of select="attendancetable/*" />
  </div>

  <div class="right">
	<table class="small">
	  <tr>
		<th>Code</th>
		<th></th>
	  </tr>
	  <tr>
		<td>/</td>
		<td><label>Present (AM)</label></td>
	  </tr>
	  <tr>
		<td>\</td>
		<td><label>Present (PM)</label></td>
	  </tr>

	  <xsl:apply-templates select="../absencecodes/code" />

<!--
	  <tr>
		<td>#</td>
		<td><label>No attendance</label></td>
	  </tr>
-->


	</table>
  </div>

  <xsl:if test="count(attendancenotes/note[comment/text()!=' '])>0">
	<div class='spacer'></div>
	<hr />
	<div class='spacer'></div>
	<div class="studenthead">
	  <div style="background-color:#fff;">
		<label>&#160;</label>
		Lesson Attendance
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

	<div class='spacer'></div>
	<div class='spacer'></div>
	<div class="center">
	  <table>
		<xsl:apply-templates select="attendancenotes/note" />
	  </table>
	</div>
  </xsl:if>



<div class='spacer'></div>
<hr />
<div class='spacer'></div>
</xsl:template>

<xsl:template match="absencecodes/code">
  <tr>
	<td>	
	  <xsl:value-of select="value/text()" />
	</td>
	<td>
	  <label>
		<xsl:value-of select="description/text()" />
	  </label>
	</td>
  </tr>
</xsl:template>


<xsl:template match="note">
  <tr>
	<th>	
	  <xsl:value-of select="date/text()" />
	</th>
	<th>
	  <xsl:value-of select="session/text()" />
	</th>
	<td>
	  <div style="margin:1px;">
		<xsl:value-of select="code/text()" />
	  </div>
	</td>
	<td>
		<xsl:value-of select="comment/text()" />
	</td>
  </tr>
</xsl:template>

</xsl:stylesheet>
