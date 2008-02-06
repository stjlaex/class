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
  <xsl:param name="homecountry">ES</xsl:param>
  <xsl:variable name="contact" select="student/contacts/contact"/>
  <xsl:for-each select="$contact">
 
	<xsl:if test="position() mod 14 = 1 and position()!=1">
	  <div class="spacer">
	  </div>
	  <hr />
	  <div class="spacer">
	  </div>
	</xsl:if>


	  <div class="label leftcol">
		<table class="address">
	  <tr>
		<td>
		  <xsl:value-of select="displayfullname/value/text()" />&#160;
		</td>
	  </tr>
	  <xsl:if test="addresses/street/value/text()!=' '">
		<tr>
		  <td>
			<xsl:value-of select="addresses/street/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="addresses/neighbourhood/value/text()!=' '">
		<tr>
		  <td>
			<xsl:value-of select="addresses/neighbourhood/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="(addresses/town/value/text()!=' ' or addresses/postcode/value/text()!='') and addresses/country/value/text()!='GB'">
		<tr>
		  <td>
			<xsl:value-of select="addresses/postcode/value/text()" />&#160;
			<xsl:value-of select="addresses/town/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="addresses/postcode/value/text()!=' ' and addresses/country/value/text()='GB'">
		<tr>
		  <td>
			<xsl:value-of select="addresses/town/value/text()" />&#160;
		  </td>
		</tr>
		<tr>
		  <td>
			<xsl:value-of select="addresses/postcode/value/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	  <xsl:if test="addresses/country/value_display/text()!=' ' and addresses/country/value/text()!=$homecountry">
		<tr>
		  <td>
			<xsl:value-of select="addresses/country/value_display/text()" />&#160;
		  </td>
		</tr>
	  </xsl:if>
	</table>
	<div class='studentdata'>
			<xsl:value-of select="../../displayfullname/value/text()" />&#160;
	</div>
  </div>

	</xsl:for-each>

	<xsl:if test="position() mod 2 = 1">
	  <div class="spacer">
	  </div>
	</xsl:if>

</xsl:template>



</xsl:stylesheet>
