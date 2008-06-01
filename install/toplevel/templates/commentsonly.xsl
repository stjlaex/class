<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for reports consisting of a written comment only for each
     subject and a summary section including attendance data and
     comments -->

<xsl:output method='html' />

<xsl:template match='text()' />


<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>

<xsl:template match="student">
  <div id="logo">
	<img src="../images/reportlogo.png" />
  </div>

<div class="studenthead">
	<table>
	  <tr>
		<td>
		  <xsl:value-of select="reports/covertitle/text()" />
		</td>
	  </tr>
	  <tr>
		<td>
		  <label>
			Date
		  </label>
		  <xsl:value-of select="reports/publishdate/text()" />
		</td>
	  </tr>
	  <tr>
		<td>&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <label>
			Student
		  </label>
		  <xsl:value-of select="displayfullname/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>		
		<td>
		  <label>
			Class
		  </label>
		  <xsl:value-of select="registrationgroup/value/text()" />&#160;
		</td>
	  </tr>
	</table>
</div>

<div class='spacer'></div>

<xsl:apply-templates select="reports"/>

<hr />

</xsl:template>

<xsl:template match="reports">

<div class="summary">
  <table class="subject">
	<tr>
	  <td>
		  <label>No. of sessions attended</label>
	  </td>
	  <td class="attcell">
		<div>&#160;
			<xsl:value-of select="attendance/summary/attended/value/text()" />
		</div>
	  </td>
	  <td>
		  <label>No. of lates</label>
	  </td>
	  <td class="attcell">
		<div>&#160;
			<xsl:value-of select="attendance/summary/late/value/text()" />
		</div>
	  </td>
	</tr>
	<tr>
	  <td>
		  <label>Out of a total of</label>
	  </td>
	  <td class="attcell">
		<div>&#160;
		  <xsl:value-of select="attendance/summary/attended/value/text() + attendance/summary/absentauthorised/value/text() + attendance/summary/absentunauthorised/value/text()" />
		</div>
	  </td>
	  <td>
		  <label>No. of absences</label>
	  </td>
	  <td class="attcell">
		<div>&#160;
			<xsl:value-of select="attendance/summary/absentauthorised/value/text() + attendance/summary/absentunauthorised/value/text()" />
		</div>
	  </td>
	</tr>
  </table>
</div>

<xsl:apply-templates select="report">
  <xsl:sort select="course/value/text()" order="descending" case-order="upper-first" />
  <xsl:sort select="component/sequence/text()" order="ascending" case-order="upper-first" />
  <xsl:sort select="component/status/text()" order="descending" case-order="upper-first" />
  <xsl:sort select="component/value/text()" order="ascending" case-order="upper-first" />
</xsl:apply-templates>

<div class="spacer"></div>

<xsl:apply-templates select="summaries/summary" />

</xsl:template>


<xsl:template match="reports/summaries/summary">
  <xsl:if test="description/type='com' and comments/text()!=' '" >
	<div class="summary">
	  <label><xsl:value-of select="description/value/text()" /></label>&#160;
	  <p class="comment-text">
		<xsl:value-of select="comments/comment/text/value/text()" />
	  </p>
	  <p class="comment-sig">
		<xsl:text>&#160;-&#160;</xsl:text>
		<xsl:value-of select="comments/comment/teacher/value/text()" />
	  </p>
	</div>
  </xsl:if>
</xsl:template>


<xsl:template match="report">
	<xsl:if test="position() mod 3 = 1 and position()!=1">
	  <div class="spacer">
	  </div>
	  <hr />
	  <div class="spacer">
	  </div>
	</xsl:if>

  <div class="subject">
	<label class="label">
	  <xsl:choose>
		<xsl:when test="component/value/text()!=' '">
		  <xsl:value-of select="component/value/text()" />
		</xsl:when>
		<xsl:otherwise>
		  <xsl:value-of select="subject/value/text()" />
		</xsl:otherwise>
	  </xsl:choose>
	</label>
	<br />

	<div class="subject-comment">
	  <p class="comment-text">
		<xsl:value-of select="comments/comment/text/value/text()" />
	  </p>
	  <xsl:text>&#160;</xsl:text>
	</div>
  </div>

</xsl:template>


</xsl:stylesheet>
