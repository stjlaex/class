<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for reporting incidents  -->

<xsl:output method='html' />

<xsl:template match='text()' />


<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>


<xsl:template match="student">

  <div class="studenthead">
	<div style="background-color:#fff;">
	  <label>&#160;</label>
	  Incident Report
	  <label>
		<xsl:value-of select="incidents/startdate/value/text()" />&#160;
		<xsl:value-of select="incidents/enddate/value/text()" />&#160;
	  </label>
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

	<xsl:apply-templates select="incidents/incident">
	  <xsl:sort select="closed/value/text()" order="ascending" case-order="upper-first" />
	  <xsl:sort select="subject/value/text()" order="ascending" case-order="upper-first" />
	</xsl:apply-templates>

<hr />

</xsl:template>


<xsl:template match="incident">
  <div class="studenthead">
	<div>
	  <label>Subject:</label>
	  <xsl:value-of select="subject/value/text()" />
	</div>
	<div>
	  <label>Date:</label>
	  <xsl:value-of select="entrydate/value/text()" />
	</div>
	<xsl:if test="closed/value/text()='N'">
	  <div>
		<label>Status:</label>
		Open
	  </div>
	</xsl:if>
  </div>
  <div class="summary">
	<label>
	  <xsl:value-of select="teacher/value/text()" />
	</label>
	<p class="comment-text">
	  <span><xsl:value-of select="sanction/value/text()" /></span>&#160;
	  <xsl:value-of select="detail/value/text()" />
	</p>
  </div>
  <xsl:apply-templates select="actions/action">
  </xsl:apply-templates>
</xsl:template>

<xsl:template match="action">
  <div class="summary">
	<label>
	  <xsl:value-of select="entrydate/value/text()" />&#160;
	  <xsl:value-of select="teacher/value/text()" />
	</label>
	<p class="comment-text">
	  <span><xsl:value-of select="sanction/value/text()" /></span>&#160;
	  <xsl:value-of select="comment/value/text()" />
	</p>
  </div>
</xsl:template>

</xsl:stylesheet>
