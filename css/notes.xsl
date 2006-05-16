<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>

		  <xsl:variable name="subconcerns" select="$concerns[subject/value=$subname]"/>


<xsl:template match="/">
	<div id='header'>
	  <p>King's Report.</p>
	</div>
<hr />
	<div id='content'>
	  <xsl:apply-templates />
	</div>
<hr />
	<div id='footer'>
	  <p>King's footer</p>
	</div>
</xsl:template>

<xsl:template match="/student">
	  <xsl:apply-templates />
</xsl:template>




<xsl:template match="assessments"> 
	<div>
		<label>
		  <xsl:value-of select="subject/label/text()" />
		</label>
		<xsl:value-of select="subject/value/text()" />
	</div>	  
</xsl:template>



<xsl:template match="assessments">
	<div>
	  <p>Subject.</p>
	</div>
<hr />
	<div class='subtable'>
	  <xsl:apply-templates />
	</div
<hr />
	<div class='subcomment'>
	  <p>Comment</p>
	</div>
</xsl:template>

