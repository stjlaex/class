<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for subject reports presented in a table with with one to several 
	 assessment grades, a second additional table displays (optional) 
	 short written comments -->

<xsl:output method='html' />

<xsl:template match='text()' />

<xsl:template match="students">
  <xsl:apply-templates select="group"/>
</xsl:template>

<xsl:template match="group">

  <div class="right">
	<div id='logo'>
	  <img src="../images/schoollogo.png" width="130px"/>
	</div>
  </div>

  <div class="spacer">
	<xsl:text>&#160;</xsl:text>
  </div>

  <table class="fullwidth grades">
	<tr>
	  <th style="width:5%;">
		<xsl:text></xsl:text>
	  </th>
	  <th colspan="3" style="width:75%;">
		<label>
		  Group:
		</label>
		<xsl:value-of select="name/value/text()" />

		&#160;&#160;
		&#160;&#160;
		<label>
		  Teacher:
		</label>
		<xsl:for-each select="tutor">
		  <xsl:value-of select="value" />&#160;&#160;
		  <xsl:if test="not(position()=last())">/&#160;&#160;</xsl:if>
		</xsl:for-each>
	  </th>
	  <th>
		<label>
		  <xsl:value-of select="date/value" />
		</label>
	  </th>
	</tr>

	<xsl:apply-templates select="student">
	  <xsl:sort select="registrationgroup/value/text()"  order="ascending"/>
	  <xsl:sort select="surname/text()"  order="ascending"/>
	</xsl:apply-templates>

	<tr>
	  <th colspan="8" class="spacer">
	  </th>
	</tr>

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
  <tr>
	<th>
	  <xsl:value-of select="position()" />
	</th>
	<td>
	  <xsl:value-of select="enrolnumber/value/text()" />
	</td>
	<td>
	  <xsl:value-of select="displayfullname/value/text()" />&#160;
	</td>
	<td>
	  <xsl:value-of select="registrationgroup/value/text()" />
	</td>
	<td>
	  <xsl:text>&#160;</xsl:text>
	</td>
  </tr>
</xsl:template>


</xsl:stylesheet>
