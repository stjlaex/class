<?xml version='1.0' encoding='utf-8' ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"  version="1.0">
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->


<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="html/body/div">
</xsl:template>


<xsl:template match="students">
  <table id="leftcol">
	<xsl:apply-templates select="student/contacts/contact[(position() mod 2)=1]">
	</xsl:apply-templates>
  </table>

  <table id="rightcol">
	<xsl:apply-templates select="student/contacts/contact[(position() mod 2)=0]">
	</xsl:apply-templates>
  </table>
</xsl:template>

<xsl:template match="contact">
  <div class="label">
	<table class="address">
	  <tr>
		<td>
		  <xsl:value-of select="displayfullname/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <xsl:value-of select="addresses/building/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <xsl:value-of select="addresses/streetno/value/text()" />&#160;
		  &#160;<xsl:value-of select="addresses/street/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <xsl:value-of select="addresses/neighbourhood/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <xsl:value-of select="addresses/town/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <xsl:value-of select="addresses/county/value/text()" />&#160;
		</td>
	  </tr>
	  <tr>
		<td>
		  <xsl:value-of select="addresses/country/value/text()" />&#160;
		</td>
	  </tr>
	</table>
	<div class='studentdata'>
			<xsl:value-of select="../../displayfullname/value/text()" />&#160;
			<xsl:value-of select="../../enrolnumber/value/text()" />&#160;
	</div>
  </div>
</xsl:template>


<xsl:template name="assheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass/label)+1"/>
  </xsl:variable>
  <xsl:variable name='asslabel' select='asstable/ass/label' />
  <xsl:if test="$index &lt; $maxindex">
	<th>
		  <label>
	  <xsl:value-of select="$asslabel[$index]" />  
	  <xsl:text>&#160; </xsl:text>
		  </label>
	</th>
    <xsl:call-template name="assheader">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>
  </xsl:if>
</xsl:template>


</xsl:stylesheet>
