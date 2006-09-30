<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for reporting comments presented in a single table displaying only the 
	 category and positive or negative rating, can parse multiple comments for 
	 each subject and combine them; written comments are not displayed -->

<xsl:output method='html' />

<xsl:template match='text()' />


<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>


<xsl:template match="student">

  <div>

	<div id="logo">
	  <img src="../images/schoollogo.png" width="130px"/>
	</div>

	<div class='studenthead'>
	  <h1>Monthly Progress Report</h1>
	  <table id='studentdata'>
		<tr>
		  <td>
			<label>
			  Student
			</label>
			<xsl:value-of select="forename/value/text()" />&#160;
			<xsl:value-of select="middlenames/value/text()" />&#160;
			<xsl:value-of select="surname/value/text()" />
		  </td>
		</tr>
		<tr>
		  <td>
			<label>
			  Form
			</label>
			<xsl:value-of select="registrationgroup/value/text()" />
			<xsl:text>&#160;&#160;&#160;&#160;&#160;&#160;</xsl:text>
			<label>Date</label>
			<xsl:value-of select="publishdate/text()" />
		  </td>
		</tr>
	  </table>
	</div>

	<div class='spacer'></div>

 	<xsl:variable name="catname" select="comments/cattable/category/name"/>
 	<xsl:variable name="comment" select="comments/comment"/>
	<xsl:variable name="subject" select="comments/subtable/subject"/>

	<div>
	  <table class="grades">
		<tr>
		  <th>
			<xsl:text></xsl:text>
		  </th>
		  <xsl:for-each select="$catname">
			<th>
			  <xsl:value-of select="."/>
			</th>
		  </xsl:for-each>
		</tr>

		<xsl:for-each select="$subject">
		  <xsl:variable name="currentsubid" select="value_db"/>
		  <tr>
			<th>
			  <xsl:value-of select="value"/>
			</th>
			<xsl:for-each select="$catname">
			  <xsl:variable name="currentcatname" select="."/>
			  <td>
				<xsl:variable name="catrating">
				  <xsl:value-of select="sum($comment[subject/value_db=$currentsubid]/categories/category[label=$currentcatname]/rating/value)" />
				</xsl:variable>
			  <xsl:choose>
				<xsl:when test="$catrating&lt;0">
				  <xsl:text>Poor</xsl:text>
				</xsl:when>
				<xsl:when test="$catrating&gt;0">
				  <xsl:text>Good</xsl:text>
				</xsl:when>
				<xsl:otherwise>
				  <xsl:text>-</xsl:text>
				</xsl:otherwise>
			  </xsl:choose>
			  </td>
			</xsl:for-each>
		  </tr>
		</xsl:for-each>

	  </table>
	</div>

	<div class='comment'>
	  Comments
	</div>

	<div class='tinycomment'>
	Year Coordinator<br />
	<p class='signature'>Signed</p>
  </div>

  <div class='tinycomment'>
	<p class='signature'>Signed</p>
  </div>

</div>
<hr />

</xsl:template>

</xsl:stylesheet>
